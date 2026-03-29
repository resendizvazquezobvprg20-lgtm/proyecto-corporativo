<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_perfils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idModulo')->constrained('modulos')->onDelete('cascade');
            $table->foreignId('idPerfil')->constrained('perfils')->onDelete('cascade');
            $table->boolean('bitAgregar')->default(false);
            $table->boolean('bitEditar')->default(false);
            $table->boolean('bitConsulta')->default(false);
            $table->boolean('bitEliminar')->default(false);
            $table->boolean('bitDetalle')->default(false);
            $table->timestamps();
            $table->unique(['idModulo', 'idPerfil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_perfils');
    }
};