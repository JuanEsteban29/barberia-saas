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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('barberias')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('cliente_nombre')->nullable();
            $table->foreignId('barbero_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->dateTime('fecha_hora');
            $table->string('estado')->default('pendiente');
            $table->string('notas_cliente')->nullable();
            $table->timestamps();
            // 👈 Aquí creamos la columna limpia con su valor por defecto
            $table->enum('metodo_pago', ['efectivo', 'transferencia'])->default('efectivo'); 
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};