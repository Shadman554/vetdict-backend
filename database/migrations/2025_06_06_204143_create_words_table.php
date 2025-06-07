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
        Schema::create('words', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->text('kurdish')->nullable();
            $table->text('arabic')->nullable();
            $table->text('description')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_saved')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('exported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
