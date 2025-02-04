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
        Schema::create('draft_product_molecule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_product_id')->constrained('draft_products')->onDelete('cascade');
            $table->foreignId('molecule_id')->constrained('molecules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_product_molecule');
    }
};
