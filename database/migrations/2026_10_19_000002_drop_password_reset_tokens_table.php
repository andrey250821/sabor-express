<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * La recuperación de contraseña no forma parte de Sabor Express.
     */
    public function up(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }

    /**
     * Permite revertir la migración si se decide restaurar la función.
     */
    public function down(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table): void {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }
};
