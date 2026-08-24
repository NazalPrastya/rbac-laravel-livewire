<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change the database session owner to match the UUID users primary key.
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
        });
    }

    /**
     * Revert the column type used by Laravel's default session migration.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
