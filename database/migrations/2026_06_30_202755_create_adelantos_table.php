<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adelantos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbero_id')->constrained('users')->onDelete('cascade');
            $table->decimal('monto', 8, 2); // El dinero que se le restará el sábado
            $table->string('motivo')->nullable(); // Ej: "Para el almuerzo"
            $table->boolean('descontado')->default(false); // Pasa a true al cerrar la semana
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adelantos');
    }
};