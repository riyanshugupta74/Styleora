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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // price at purchase
            $table->decimal('total', 10, 2);
            $table->string('product_name_snapshot')->nullable();
            $table->string('variant_sku_snapshot')->nullable();
            $table->string('color_snapshot')->nullable();
            $table->string('size_snapshot')->nullable();
            $table->string('image_snapshot')->nullable();
            $table->string('status')->default('placed'); // placed, confirmed, packed, shipped, out_for_delivery, delivered, cancelled, return_requested, returned
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
