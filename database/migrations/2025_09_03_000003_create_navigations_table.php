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
        Schema::create('navigations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('page_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->string('target')->default('_self'); // _self, _blank
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('location')->default('header'); // header, footer, sidebar
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('navigations')->onDelete('cascade');
            $table->index(['location', 'is_active', 'sort_order']);
            $table->index(['parent_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigations');
    }
};
