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
        Schema::table('cierres_diarios', function (Blueprint $table) {
            $table->decimal('efectivo_usd_contado', 10, 2)->nullable();
            $table->decimal('efectivo_bs_contado', 10, 2)->nullable();
            $table->decimal('transferencia_contado', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cierres_diarios', function (Blueprint $table) {
            $table->dropColumn(['efectivo_usd_contado', 'efectivo_bs_contado', 'transferencia_contado']);
        });
    }
};
