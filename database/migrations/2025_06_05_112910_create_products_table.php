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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid',50)->unique();
            $table->string('slug',150)->unique();
            $table->string('name',200);
            $table->string('version',100);
            $table->string('icon')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('type', ['core', 'addon'])->default('core');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
