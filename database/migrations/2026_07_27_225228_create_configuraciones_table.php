<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('configuraciones', function (Blueprint $table) {


            $table->id();


            $table->string('nombre_restaurante')
                  ->default('Sabor Express');


            $table->string('telefono')
                  ->nullable();


            $table->text('direccion')
                  ->nullable();


            $table->string('logo')
                  ->nullable();


            // Nuevo campo para guardar QR de pago
            $table->string('qr_pago')
                  ->nullable();


            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }

};