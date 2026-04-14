<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->string('strRuta', 150)->nullable()->after('strNombreModulo');
            $table->unsignedBigInteger('idMenu')->nullable()->after('strRuta');
            $table->foreign('idMenu')->references('id')->on('menus')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropForeign(['idMenu']);
            $table->dropColumn(['strRuta', 'idMenu']);
        });
    }
};