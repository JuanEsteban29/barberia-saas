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
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_id')->constrained('cortes')->onDelete('cascade');
            $table->foreignId('barbero_id')->constrained('users')->onDelete('cascade');
            $table->decimal('monto_barbero', 10, 2);
            $table->decimal('monto_negocio', 10, 2);
            $table->decimal('porcentaje_barbero', 5, 2)->default(60.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};
