<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idMenu')->constrained('menu')->onDelete('cascade');
            $table->foreignId('idModulo')->constrained('modulo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_modulo');
    }
};