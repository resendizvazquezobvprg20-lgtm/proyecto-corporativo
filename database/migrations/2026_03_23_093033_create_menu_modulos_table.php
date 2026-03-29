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
            $table->foreignId('idMenu')->constrained('menus')->onDelete('cascade');
            $table->foreignId('idModulo')->constrained('modulos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_modulos');
    }
};