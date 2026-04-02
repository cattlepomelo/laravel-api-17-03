<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Models\GroupInternship;
use App\Models\Application;
use App\Models\Evaluation;
use App\Models\ActivityLog;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create groups
        $group1 = Group::create(['name' => 'IT Group 1']);
        $group2 = Group::create(['name' => 'IT Group 2']);

        // Create companies
        $company1 = Company::create(['name' => 'Tech Corp']);
        $company2 = Company::create(['name' => 'Digital Solutions']);
        $company3 = Company::create(['name' => 'StartUp Hub']);

        // Create internships
        $internship1 = Internship::create([
            'name' => 'Web Development Internship',
            'goals' => 'Learn modern web development practices',
            'evaluation_type' => 'balles'
        ]);

        $internship2 = Internship::create([
            'name' => 'Mobile Development Internship',
            'goals' => 'Build mobile applications',
            'evaluation_type' => 'procenti'
        ]);

        $internship3 = Internship::create([
            'name' => 'DevOps Internship',
            'goals' => 'Learn CI/CD and cloud infrastructure',
            'evaluation_type' => 'i/ni'
        ]);

        // Create users
        // Students
        $student1 = User::create([
            'name' => 'John Student',
            'role' => 'student',
            'group_id' => $group1->id,
            'company_id' => $company1->id
        ]);

        $student2 = User::create([
            'name' => 'Jane Student',
            'role' => 'student',
            'group_id' => $group1->id,
            'company_id' => $company2->id
        ]);

        $student3 = User::create([
            'name' => 'Bob Student',
            'role' => 'student',
            'group_id' => $group2->id,
            'company_id' => null
        ]);

        // School supervisors
        $supervisor1 = User::create([
            'name' => 'Prof. Alice Supervisor',
            'role' => 'school_supervisor',
            'group_id' => $group1->id,
            'company_id' => null
        ]);

        $supervisor2 = User::create([
            'name' => 'Prof. Charlie Mentor',
            'role' => 'school_supervisor',
            'group_id' => $group2->id,
            'company_id' => null
        ]);

        // Company supervisors
        $companySupervisor1 = User::create([
            'name' => 'Mike Manager',
            'role' => 'company_supervisor',
            'group_id' => null,
            'company_id' => $company1->id
        ]);

        $companySupervisor2 = User::create([
            'name' => 'Sarah Director',
            'role' => 'company_supervisor',
            'group_id' => null,
            'company_id' => $company2->id
        ]);

        // Link groups to internships
        GroupInternship::create([
            'group_id' => $group1->id,
            'internship_id' => $internship1->id,
            'start_at' => '2026-01-01',
            'end_at' => '2026-12-31'
        ]);

        GroupInternship::create([
            'group_id' => $group1->id,
            'internship_id' => $internship2->id,
            'start_at' => '2026-03-01',
            'end_at' => '2026-09-30'
        ]);

        GroupInternship::create([
            'group_id' => $group2->id,
            'internship_id' => $internship3->id,
            'start_at' => '2026-02-01',
            'end_at' => '2026-08-31'
        ]);

        // Create some applications
        $application1 = Application::create([
            'user_id' => $student1->id,
            'group_id' => $group1->id,
            'internship_id' => $internship1->id,
            'company_id' => $company1->id,
            'motivation_letter' => 'I am very interested in web development and want to learn from the best.',
            'status' => 'approved',
            'approved_at' => now()
        ]);

        $application2 = Application::create([
            'user_id' => $student2->id,
            'group_id' => $group1->id,
            'internship_id' => $internship1->id,
            'company_id' => $company2->id,
            'motivation_letter' => 'Mobile development is my passion. I want to build apps that matter.',
            'status' => 'pending'
        ]);

        $application3 = Application::create([
            'user_id' => $student3->id,
            'group_id' => $group2->id,
            'internship_id' => $internship3->id,
            'company_id' => $company3->id,
            'motivation_letter' => 'DevOps is the future, and I want to be part of it.',
            'status' => 'pending'
        ]);

        // Create evaluations
        Evaluation::create([
            'application_id' => $application1->id,
            'date' => '2026-03-01',
            'comment' => 'Excellent progress',
            'grade' => '9'
        ]);

        Evaluation::create([
            'application_id' => $application1->id,
            'date' => '2026-03-15',
            'comment' => 'Keep up the good work',
            'grade' => '10'
        ]);

        // Create activity logs
        ActivityLog::create([
            'user_id' => $student1->id,
            'action' => 'application_created',
            'entity_type' => Application::class,
            'entity_id' => $application1->id,
            'properties' => ['internship_id' => $internship1->id],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0'
        ]);

        ActivityLog::create([
            'user_id' => $student2->id,
            'action' => 'application_submitted',
            'entity_type' => Application::class,
            'entity_id' => $application2->id,
            'properties' => ['internship_id' => $internship1->id],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0'
        ]);

        $this->command->info("✓ Test data seeded successfully!");
        $this->command->info("");
        $this->command->info("Groups:");
        $this->command->info("  - Group 1: ID {$group1->id}");
        $this->command->info("  - Group 2: ID {$group2->id}");
        $this->command->info("");
        $this->command->info("Companies:");
        $this->command->info("  - Tech Corp: ID {$company1->id}");
        $this->command->info("  - Digital Solutions: ID {$company2->id}");
        $this->command->info("  - StartUp Hub: ID {$company3->id}");
        $this->command->info("");
        $this->command->info("Internships:");
        $this->command->info("  - Web Development: ID {$internship1->id}");
        $this->command->info("  - Mobile Development: ID {$internship2->id}");
        $this->command->info("  - DevOps: ID {$internship3->id}");
        $this->command->info("");
        $this->command->info("Users:");
        $this->command->info("  - John Student (student, group {$group1->id}): ID {$student1->id}");
        $this->command->info("  - Jane Student (student, group {$group1->id}): ID {$student2->id}");
        $this->command->info("  - Bob Student (student, group {$group2->id}): ID {$student3->id}");
        $this->command->info("  - Prof. Alice Supervisor (school_supervisor): ID {$supervisor1->id}");
        $this->command->info("  - Mike Manager (company_supervisor): ID {$companySupervisor1->id}");
        $this->command->info("");
        $this->command->info("Applications:");
        $this->command->info("  - Application 1 (approved): ID {$application1->id}");
        $this->command->info("  - Application 2 (pending): ID {$application2->id}");
        $this->command->info("  - Application 3 (pending): ID {$application3->id}");
    }
}
