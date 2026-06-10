<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_content_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id');
            $table->string('slug');
            $table->json('content');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_content_sections');
    }
};
