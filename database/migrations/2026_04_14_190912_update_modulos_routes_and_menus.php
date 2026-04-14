<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Actualizar rutas de los módulos mediante un array para evitar repetir código
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
            DB::table('modulos')
                ->where('id', $id)
                ->update(['strRuta' => $ruta]);
        }

        // 2. Sincronizar idMenu con la tabla menu_modulos
        // Usamos DB::statement para ejecutar SQL puro directamente
        DB::statement("
            UPDATE modulos m
            JOIN menu_modulos mm ON mm.idModulo = m.id
            SET m.idMenu = mm.idMenu
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: Limpiar las rutas si decides revertir la migración
        DB::table('modulos')->whereIn('id', range(1, 8))->update(['strRuta' => null]);
    }
};