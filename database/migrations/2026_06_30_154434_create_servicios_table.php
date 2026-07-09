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
    Schema::create('servicios', function (Blueprint $table) {
        $table->id();
        // Conectamos el servicio directamente con su barbería
        $table->foreignId('barberia_id')->constrained('barberias')->onDelete('cascade');
        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->decimal('precio', 8, 2); // Ejemplo: 15.50
        $table->integer('duracion'); // Duración aproximada en minutos (ej: 30, 45)
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
