<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('roles', 'descripcion')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }

        if (Schema::hasColumn('categorias', 'descripcion')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('roles', 'descripcion')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('descripcion')
                    ->nullable()
                    ->after('nombre');
            });
        }

        if (!Schema::hasColumn('categorias', 'descripcion')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->text('descripcion')
                    ->nullable()
                    ->after('nombre');
            });
        }
    }
};
