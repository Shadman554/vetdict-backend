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
        Schema::create('normal_ranges', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('unit');
            $table->string('min_value');
            $table->string('max_value');
            $table->string('species');
            $table->string('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('normal_ranges');
    }
};
