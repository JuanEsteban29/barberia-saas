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
    Schema::create('barberias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('slug')->unique();
        $table->string('direccion')->nullable();
        $table->string('telefono')->nullable();
        $table->string('logo')->nullable();
        // 60% para el barbero, 40% para la casa por defecto
        $table->unsignedTinyInteger('porcentaje_barbero')->default(60); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barberias');
    }
};
