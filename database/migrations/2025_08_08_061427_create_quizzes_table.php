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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->integer('time_limit')->nullable()->comment('in minutes');
            $table->integer('max_attempts')->default(1);
            $table->enum('shuffle_questions', ['0', '1'])->default('0')->comment("0=no, 1=yes");
            $table->enum('show_results_immediately', ['0', '1'])->default('1')->comment("0=no, 1=yes");
            $table->enum('status', ['0', '1', '2'])->default('0')->comment("0=draft, 1=active, 2=archived");
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
