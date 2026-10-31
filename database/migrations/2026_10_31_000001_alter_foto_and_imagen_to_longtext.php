<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->longText('foto')->nullable()->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->longText('imagen')->nullable()->change();
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->longText('imagen')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('imagen', 255)->nullable()->change();
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->string('imagen', 255)->nullable()->change();
        });
    }
};
