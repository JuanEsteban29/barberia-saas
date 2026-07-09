<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos CREATE para fundar la tabla desde cero
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea que causaba el error del Dashboard
            $table->foreignId('barberia_id')
                  ->constrained('barberias')
                  ->onDelete('cascade');

            $table->string('descripcion');
            $table->string('categoria'); // servicios, insumos, etc.
            $table->decimal('monto', 10, 2);
            $table->timestamp('fecha_gasto')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};