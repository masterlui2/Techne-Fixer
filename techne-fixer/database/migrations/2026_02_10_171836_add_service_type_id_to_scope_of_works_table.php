<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scope_of_works', function (Blueprint $table) {
            // Add the service_type_id column and foreign key
            $table->foreignId('service_type_id')
                  ->nullable()
                  ->after('service_id')
                  ->constrained()
                  ->cascadeOnDelete();
            
            // Add new composite index (keeping the old one)
            $table->index(['service_id', 'service_type_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scope_of_works', function (Blueprint $table) {
            // Drop the new index first
            $table->dropIndex(['service_id', 'service_type_id', 'order']);
            
            // Drop foreign key and column
            $table->dropForeign(['service_type_id']);
            $table->dropColumn('service_type_id');
        });
    }
};