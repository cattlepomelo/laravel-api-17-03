<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Models\GroupInternship;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create group
        $group = Group::create(['name' => 'Test Group']);

        // Create company
        $company = Company::create(['name' => 'Test Company']);

        // Create internship
        $internship = Internship::create([
            'name' => 'Test Internship',
            'evaluation_type' => 'balles'
        ]);

        // Create student user
        $user = User::create([
            'name' => 'Test Student',
            'role' => 'student',
            'group_id' => $group->id,
            'company_id' => $company->id
        ]);

        // Link group to internship
        GroupInternship::create([
            'group_id' => $group->id,
            'internship_id' => $internship->id,
            'start_at' => '2026-01-01',
            'end_at' => '2026-12-31'
        ]);

        $this->command->info("Test data seeded successfully!");
        $this->command->info("User ID: {$user->id}, Internship ID: {$internship->id}, Group ID: {$group->id}");
    }
}
