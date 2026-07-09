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
    Schema::table('citas', function (Blueprint $table) {
        // Cambiamos el enum para incluir 'fiado'
        // Nota: Si usas MySQL, a veces es necesario instalar 'doctrine/dbal' para usar ->change()
        $table->enum('metodo_pago', ['efectivo', 'transferencia', 'fiado'])->default('efectivo')->change();
        
        // Columna para saber si ya se liquidó la deuda
        $table->boolean('pago_completado')->default(true);
    });
}
    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('citas', function (Blueprint $table) {
        $table->dropColumn('pago_completado');
        // Opcional: revertir el enum si tu base de datos lo permite
    });
}
};
