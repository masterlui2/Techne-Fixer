<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->json('services')->nullable()->comment('Array of {name, color} badges');
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};