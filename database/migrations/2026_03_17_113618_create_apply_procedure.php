<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS apply_for_internship
        ");

        DB::unprepared("
            CREATE PROCEDURE apply_for_internship(
                IN p_user_id INT,
                IN p_internship_id INT,
                IN p_group_id INT,
                IN p_company_id INT,
                IN p_motivation_letter TEXT,
                OUT result TEXT
            )
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM users WHERE id = p_user_id) THEN
                    SET result = 'User not found';
                ELSEIF NOT EXISTS (SELECT 1 FROM internships WHERE id = p_internship_id) THEN
                    SET result = 'Invalid internship';
                ELSEIF EXISTS (
                    SELECT 1 FROM applications 
                    WHERE user_id = p_user_id 
                    AND internship_id = p_internship_id
                    AND status IN ('pending', 'approved')
                ) THEN
                    SET result = 'Already applied';
                ELSE
                    INSERT INTO applications(
                        user_id, internship_id, group_id, company_id, 
                        motivation_letter, status
                    )
                    VALUES (
                        p_user_id, p_internship_id, p_group_id, p_company_id, 
                        p_motivation_letter, 'pending'
                    );
                    SET result = 'OK';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS apply_for_internship');
    }
};
