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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->enum('type', ['1', '2', '3', '4'])->default('1')->comment("1=multiple_choice, 2=true_false, 3=text, 4=essay");
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->string('image')->nullable();
            $table->enum('difficulty', ['1', '2', '3'])->default('1')->comment("1=easy, 2=medium, 3=hard");
            $table->enum('status', ['0', '1'])->default('1')->comment("0=inactive, 1=active");
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
