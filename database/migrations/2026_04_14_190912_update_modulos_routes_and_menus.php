<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PASO 1: Agregar columnas primero
        Schema::table('modulos', function (Blueprint $table) {
            $table->string('strRuta', 150)->nullable()->after('strNombreModulo');
            $table->unsignedBigInteger('idMenu')->nullable()->after('strRuta');
            $table->foreign('idMenu')->references('id')->on('menus')->nullOnDelete();
        });

        // PASO 2: Backfill de rutas en módulos existentes
        // Ajusta los IDs si en tu BD son diferentes
        $rutas = [
            1 => '/seguridad/perfil',
            2 => '/seguridad/modulo',
            3 => '/seguridad/permisos-perfil',
            4 => '/seguridad/usuario',
            5 => '/principal1/sub1',
            6 => '/principal1/sub2',
            7 => '/principal2/sub1',
            8 => '/principal2/sub2',
        ];

        foreach ($rutas as $id => $ruta) {
            DB::table('modulos')->where('id', $id)->update(['strRuta' => $ruta]);
        }

        // PASO 3: Sincronizar idMenu desde menu_modulos existente
        $links = DB::table('menu_modulos')->get();
        foreach ($links as $link) {
            DB::table('modulos')
                ->where('id', $link->idModulo)
                ->whereNull('idMenu')
                ->update(['idMenu' => $link->idMenu]);
        }
    }

    public function down(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropForeign(['idMenu']);
            $table->dropColumn(['strRuta', 'idMenu']);
        });
    }
};