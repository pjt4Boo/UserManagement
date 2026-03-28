<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create ENUM types for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE TYPE role_enum AS ENUM ('admin', 'user')");
            DB::statement("CREATE TYPE status_enum AS ENUM ('active', 'inactive')");
        }

        Schema::table('users', function (Blueprint $table) {
            // Add role and status columns
            if (DB::getDriverName() === 'pgsql') {
                // Use native ENUM type for PostgreSQL
                DB::statement("ALTER TABLE users ADD COLUMN role role_enum DEFAULT 'user'");
                DB::statement("ALTER TABLE users ADD COLUMN status status_enum DEFAULT 'active'");
            } else {
                // Fallback for other databases
                $table->string('role')->default('user');
                $table->string('status')->default('active');
            }
            
            // Add soft delete support
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP COLUMN role');
            DB::statement('ALTER TABLE users DROP COLUMN status');
            DB::statement('DROP TYPE role_enum');
            DB::statement('DROP TYPE status_enum');
        }
    }
};
