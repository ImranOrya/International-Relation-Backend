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
        DB::statement("SET SESSION sql_mode = 'NO_BACKSLASH_ESCAPES';");
        DB::statement("
         CREATE VIEW users_fa_view AS
        SELECT 
            u.id,
            u.username,
            u.profile,
            stt.name AS status,
            u.created_at,
            e.value AS email,
            c.value AS contact,
            td.destination,
            tj.job
        FROM users u
        LEFT JOIN user_details ud ON u.id = ud.user_id
        LEFT JOIN contacts c ON u.contact_id = c.id
        LEFT JOIN emails e ON u.email_id = e.id
        LEFT JOIN user_statuses us ON u.id = us.user_id
        LEFT JOIN status_types st ON us.status_type_id = st.id
        LEFT JOIN status_type_trans stt 
            ON st.id = stt.status_type_id AND stt.language_name = 'fa'
        LEFT JOIN 
            (SELECT translable_id, MAX(value) AS destination
            FROM translates
            WHERE translable_type = 'App\\Models\\Destination' 
            AND language_name = 'fa'
            GROUP BY translable_id) td 
        ON ud.destination_id = td.translable_id
        LEFT JOIN 
            (SELECT translable_id, MAX(value) AS job
            FROM translates
            WHERE translable_type = 'App\\Models\\ModelJob' 
            AND language_name = 'fa'
            GROUP BY translable_id) tj
        ON ud.job_id = tj.translable_id;

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS users_fa_view');
    }
};
