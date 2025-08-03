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
        Schema::create('unit_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignUuid('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignUuid('unit_item_id')->constrained('unit_items')->onDelete('cascade');
            $table->string('borrowed_by');
            $table->datetime('borrowed_at');
            $table->datetime('returned_at')->nullable();
            $table->text('purpose');
            $table->integer('room');
            $table->boolean('status')->default(true); // true for borrowed, false for returned
            $table->string('signature')->nullable();
            $table->enum('guarantee', ['BKP', 'kartu pelajar'])->default('kartu pelajar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_loans');
    }
};
