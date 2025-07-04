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
        Schema::create('diseases', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('kurdish');
            $table->text('symptoms');
            $table->text('cause')->nullable();
            $table->text('control')->nullable();
            $table->text('treatment')->nullable();
            $table->text('prevention')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
