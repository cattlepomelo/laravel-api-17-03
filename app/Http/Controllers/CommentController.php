<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Get comments for a specific model type and ID.
     */
    public function index(string $type, int $id): JsonResponse
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid commentable type'], 400);
        }

        $model = $modelClass::find($id);

        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $comments = $model->comments()->with('user')->latest()->get();

        return response()->json([
            'data' => $comments->map(fn($comment) => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->toIso8601String(),
            ])
        ]);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'commentable_type' => 'required|in:applications,evaluations',
            'commentable_id' => 'required|integer',
            'comment' => 'required|string|max:1000',
        ]);

        $modelClass = $this->getModelClass($request->commentable_type);

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid commentable type'], 400);
        }

        $model = $modelClass::find($request->commentable_id);

        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $comment = $model->comments()->create([
            'user_id' => 1, // In real app: $request->user()->id
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->toIso8601String(),
            ]
        ], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Get the model class from the type string.
     */
    private function getModelClass(string $type): ?string
    {
        return match ($type) {
            'applications' => \App\Models\Application::class,
            'evaluations' => \App\Models\Evaluation::class,
            default => null,
        };
    }
}
