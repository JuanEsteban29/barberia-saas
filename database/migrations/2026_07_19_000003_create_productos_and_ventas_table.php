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
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('barberia_id')->constrained('barberias')->onDelete('cascade');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->decimal('precio_compra', 10, 2)->default(0);
                $table->decimal('precio_venta', 10, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ventas_productos')) {
            Schema::create('ventas_productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('barberia_id')->constrained('barberias')->onDelete('cascade');
                $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
                $table->foreignId('barbero_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('cierre_diario_id')->nullable()->constrained('cierres_diarios')->onDelete('set null');
                $table->integer('cantidad')->default(1);
                $table->decimal('precio_unitario', 10, 2)->default(0);
                $table->decimal('comision_barbero', 10, 2)->default(0);
                $table->string('metodo_pago');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_productos');
        Schema::dropIfExists('productos');
    }
};
