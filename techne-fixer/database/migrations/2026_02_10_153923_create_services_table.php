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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            
            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable()->comment('e.g., LAPTOP, PRINTER');
            $table->text('description');
            $table->text('long_description')->nullable();
            $table->string('icon')->nullable()->comment('Icon identifier or emoji');
            
            
            // Status & Display
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            
            // Media
            $table->string('image')->nullable();
            $table->json('gallery_images')->nullable()->comment('Additional service images');
            
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('featured');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};