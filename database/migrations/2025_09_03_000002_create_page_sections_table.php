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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, image, video, hero, cards, etc.
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // For additional configuration
            $table->timestamps();
            $table->softDeletes();

            $table->index(['page_id', 'sort_order']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
