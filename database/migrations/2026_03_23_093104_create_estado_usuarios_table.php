<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('strNombre', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_usuario');
    }
};