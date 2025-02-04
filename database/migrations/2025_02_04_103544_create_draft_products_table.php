<?php

use App\Constants\DraftProductStatus;
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
        Schema::create('draft_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('manufacturer');
            $table->decimal('price', 8, 2);
            $table->decimal('mrp', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_assured')->default(false);
            $table->boolean('is_discountinued')->default(false);
            $table->boolean('is_refrigerated')->default(false);
            $table->boolean('is_published')->default(false);
            $table->enum('status', DraftProductStatus::STATUSES)->default(DraftProductStatus::DRAFT);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_products');
    }
};