<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('strNombreUsuario', 100)->unique();
        
        // Asegúrate que la tabla se llame 'perfils' en su migración original
        $table->foreignId('idPerfil')->constrained('perfils')->onDelete('restrict');
        
        $table->string('strPwd');
        
        // Relación con estado_usuarios
        $table->unsignedBigInteger('idEstadoUsuario')->default(1);
        $table->foreign('idEstadoUsuario')->references('id')->on('estado_usuarios');

        $table->string('strCorreo', 150)->unique();
        $table->string('strNumeroCelular', 20)->nullable();
        $table->string('strImagen', 255)->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};