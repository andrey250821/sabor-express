<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('pedido_id')
                ->nullable()
                ->constrained('pedidos')
                ->cascadeOnDelete();

            $table->text('mensaje');

            $table->enum('tipo', [
                'cliente',
                'administrador',
                'delivery',
            ]);

            $table->string('evento');

            $table->boolean('leido')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
