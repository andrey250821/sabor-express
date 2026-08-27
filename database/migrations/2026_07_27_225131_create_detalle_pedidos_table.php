<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('detalle_pedidos', function (Blueprint $table) {


            $table->id();



            $table->foreignId('pedido_id')
                  ->constrained('pedidos')
                  ->cascadeOnDelete();



            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->cascadeOnDelete();



            $table->integer('cantidad');



            // Precio guardado al momento de compra
            // aunque después cambie el producto

            $table->decimal('precio',10,2);



            $table->decimal('subtotal',10,2);



            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }

};