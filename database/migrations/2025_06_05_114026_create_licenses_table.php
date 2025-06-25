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
        Schema::create('licenses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('raw_key',64)->unique()->nullable();
            $table->string('activated_domain')->nullable();
            $table->string('activated_ip', 100)->nullable();
            $table->boolean('is_activated')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('product_uuid',150);
            $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('restrict');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->enum('status', ['active', 'revoked', 'expired', 'refunded'])->default('active');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->index([
                'raw_key',
                'activated_domain',
                'activated_ip',
                'is_activated',
                'status',
            ], 'license_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
