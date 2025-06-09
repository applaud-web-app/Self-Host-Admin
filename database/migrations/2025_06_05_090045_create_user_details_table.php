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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            // Foreign key to users table:
            $table->unsignedBigInteger('user_id')->index();
            $table->string('billing_name', 255);
            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('pin_code', 10);
            $table->text('address');
            $table->string('pan_card', 20)->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->timestamps();

            // Foreign key constraint:
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
