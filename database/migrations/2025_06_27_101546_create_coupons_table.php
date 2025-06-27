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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code')->unique();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_amount', 8, 2);
            $table->date('expiry_date');
            $table->enum('usage_type', ['single', 'multiple']);
            $table->integer('usage_limit')->default(1);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
