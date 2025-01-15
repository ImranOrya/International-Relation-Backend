<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        CREATE VIEW users_en_view AS
        SELECT 
            u.id,
            u.username,
            u.profile,
            stt.name AS status,
            u.created_at,
            e.value as email,
			c.value as contact,
            d.name as destination,
            j.name as job
        FROM users u
        LEFT JOIN user_details ud ON u.id = ud.user_id
        
        LEFT JOIN contacts c ON u.contact_id = c.id
         LEFT JOIN emails e ON u.email_id = e.id
        LEFT JOIN user_statuses us ON u.id = us.user_id
        LEFT JOIN status_types st ON us.status_type_id = st.id
        LEFT JOIN status_type_trans stt 
            ON st.id = stt.status_type_id AND stt.language_name = "en"
        LEFT JOIN model_jobs j ON ud.job_id = j.id
        LEFT JOIN destinations d ON ud.destination_id = d.id;
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS users_en_view');
    }
};
