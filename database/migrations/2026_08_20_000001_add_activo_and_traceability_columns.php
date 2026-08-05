<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        // 1. Agregar activo a usuarios si no existe
        Schema::table('usuarios', function (Blueprint $table) {
            if (! Schema::hasColumn('usuarios', 'activo')) {
                $table->boolean('activo')->default(true)->after('contrasena');
            }
        });

        // 2. Agregar id_usuario y timestamps a compras si no existen
        Schema::table('compras', function (Blueprint $table) {
            if (! Schema::hasColumn('compras', 'id_usuario')) {
                $table->unsignedBigInteger('id_usuario')->nullable()->after('id_compra');
                $table->foreign('id_usuario')
                      ->references('id_usuario')
                      ->on('usuarios')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');
            }

            if (! Schema::hasColumn('compras', 'created_at')) {
                $table->timestamps();
            }
        });

        // 3. Agregar timestamps a ventas si no existen
        Schema::table('ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'activo')) {
                $table->dropColumn('activo');
            }
        });

        Schema::table('compras', function (Blueprint $table) {
            if (Schema::hasColumn('compras', 'id_usuario')) {
                $table->dropForeign(['id_usuario']);
                $table->dropColumn('id_usuario');
            }
            if (Schema::hasColumn('compras', 'created_at')) {
                $table->dropTimestamps();
            }
        });

        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
