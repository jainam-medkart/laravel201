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
        Schema::create('published_product_molecule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('published_product_id')->constrained('published_products')->onDelete('cascade');
            $table->foreignId('molecule_id')->constrained('molecules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('published_product_molecule');
    }
};