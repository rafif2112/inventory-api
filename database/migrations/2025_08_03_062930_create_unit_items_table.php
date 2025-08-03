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
        Schema::create('unit_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sub_item_id')->constrained('sub_items')->onDelete('cascade');
            $table->string('code_unit')->unique();
            $table->text('description')->nullable();
            $table->date('procurement_date');
            $table->boolean('status')->default(true); // true for available, false for not available
            $table->boolean('condition')->default(true); // true for good condition, false for damaged
            $table->string('barcode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_items');
    }
};
