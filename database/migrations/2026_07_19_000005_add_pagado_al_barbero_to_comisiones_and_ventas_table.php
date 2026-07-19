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
        Schema::table('comisiones', function (Blueprint $table) {
            $table->boolean('pagado_al_barbero')->default(false)->after('porcentaje_barbero');
        });

        Schema::table('ventas_productos', function (Blueprint $table) {
            $table->boolean('pagado_al_barbero')->default(false)->after('comision_barbero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn('pagado_al_barbero');
        });

        Schema::table('ventas_productos', function (Blueprint $table) {
            $table->dropColumn('pagado_al_barbero');
        });
    }
};
