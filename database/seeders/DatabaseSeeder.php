<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Estados de usuario ────────────────────────────
        DB::table('estado_usuarios')->insert([
            ['id' => 1, 'strNombre' => 'Activo'],
            ['id' => 2, 'strNombre' => 'Inactivo'],
        ]);

        // ── Perfiles ──────────────────────────────────────
        DB::table('perfils')->insert([
            ['id' => 1, 'strNombrePerfil' => 'Administrador',   'bitAdministrador' => true,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'strNombrePerfil' => 'Gerente',         'bitAdministrador' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'strNombrePerfil' => 'Supervisor',      'bitAdministrador' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'strNombrePerfil' => 'Vendedor',        'bitAdministrador' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'strNombrePerfil' => 'Soporte Técnico', 'bitAdministrador' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Usuarios ──────────────────────────────────────
        DB::table('usuarios')->insert([
            [
                'strNombreUsuario' => 'admin',
                'idPerfil'         => 1,
                'strPwd'           => Hash::make('Admin@1234'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'admin@corporativo.com',
                'strNumeroCelular' => '5512345678',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'jgarcia',
                'idPerfil'         => 2,
                'strPwd'           => Hash::make('Gerente@123'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'jgarcia@corporativo.com',
                'strNumeroCelular' => '5598765432',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'mlopez',
                'idPerfil'         => 3,
                'strPwd'           => Hash::make('Supervisor@123'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'mlopez@corporativo.com',
                'strNumeroCelular' => '5511223344',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'rperez',
                'idPerfil'         => 4,
                'strPwd'           => Hash::make('Vendedor@123'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'rperez@corporativo.com',
                'strNumeroCelular' => '5544332211',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'ltorres',
                'idPerfil'         => 4,
                'strPwd'           => Hash::make('Vendedor@123'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'ltorres@corporativo.com',
                'strNumeroCelular' => '5566778899',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'cmorales',
                'idPerfil'         => 5,
                'strPwd'           => Hash::make('Soporte@123'),
                'idEstadoUsuario'  => 1,
                'strCorreo'        => 'cmorales@corporativo.com',
                'strNumeroCelular' => '5577889900',
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'strNombreUsuario' => 'prueba_inactivo',
                'idPerfil'         => 4,
                'strPwd'           => Hash::make('Inactivo@123'),
                'idEstadoUsuario'  => 2, // Inactivo — para probar el bloqueo en login
                'strCorreo'        => 'inactivo@corporativo.com',
                'strNumeroCelular' => null,
                'strImagen'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // ── Módulos ───────────────────────────────────────
        DB::table('modulos')->insert([
            ['id' => 1, 'strNombreModulo' => 'Perfil',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'strNombreModulo' => 'Módulo',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'strNombreModulo' => 'Permisos Perfil', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'strNombreModulo' => 'Usuario',         'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'strNombreModulo' => 'Ventas',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'strNombreModulo' => 'Clientes',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'strNombreModulo' => 'Inventario',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'strNombreModulo' => 'Reportes',   'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Menús ─────────────────────────────────────────
        DB::table('menus')->insert([
            ['id' => 1, 'strNombreMenu' => 'Seguridad',   'strIcono' => 'bi-shield-lock', 'intOrden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'strNombreMenu' => 'Comercial', 'strIcono' => 'bi-shop',        'intOrden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'strNombreMenu' => 'Operaciones', 'strIcono' => 'bi-box-seam',      'intOrden' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Relación Menú → Módulo ────────────────────────
        DB::table('menu_modulos')->insert([
            ['idMenu' => 1, 'idModulo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 1, 'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 1, 'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 1, 'idModulo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 2, 'idModulo' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 2, 'idModulo' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 3, 'idModulo' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['idMenu' => 3, 'idModulo' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Permisos por Perfil ───────────────────────────

        // Administrador — acceso total a todo
        for ($modId = 1; $modId <= 8; $modId++) {
            DB::table('permisos_perfils')->insert([
                'idModulo' => $modId, 'idPerfil' => 1,
                'bitAgregar' => true, 'bitEditar' => true, 'bitConsulta' => true,
                'bitEliminar' => true, 'bitDetalle' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Gerente — acceso total a Principal 1 y 2, solo consulta en Seguridad
        DB::table('permisos_perfils')->insert([
            // Seguridad: solo consulta y detalle
            ['idModulo' => 1, 'idPerfil' => 2, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 2, 'idPerfil' => 2, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 4, 'idPerfil' => 2, 'bitAgregar' => false, 'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            // Principal 1 y 2: acceso completo
            ['idModulo' => 5, 'idPerfil' => 2, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 6, 'idPerfil' => 2, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 7, 'idPerfil' => 2, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 8, 'idPerfil' => 2, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Supervisor — Principal 1 completo, Principal 2 solo consulta
        DB::table('permisos_perfils')->insert([
            ['idModulo' => 5, 'idPerfil' => 3, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 6, 'idPerfil' => 3, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 7, 'idPerfil' => 3, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 8, 'idPerfil' => 3, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Vendedor — solo Principal 1.1 con acceso básico
        DB::table('permisos_perfils')->insert([
            ['idModulo' => 5, 'idPerfil' => 4, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 6, 'idPerfil' => 4, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Soporte Técnico — Principal 2 completo, Principal 1 solo consulta
        DB::table('permisos_perfils')->insert([
            ['idModulo' => 5, 'idPerfil' => 5, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 6, 'idPerfil' => 5, 'bitAgregar' => false, 'bitEditar' => false, 'bitConsulta' => true,  'bitEliminar' => false, 'bitDetalle' => false, 'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 7, 'idPerfil' => 5, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idModulo' => 8, 'idPerfil' => 5, 'bitAgregar' => true,  'bitEditar' => true,  'bitConsulta' => true,  'bitEliminar' => true,  'bitDetalle' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}