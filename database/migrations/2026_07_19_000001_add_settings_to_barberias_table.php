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
        Schema::table('barberias', function (Blueprint $table) {
            if (!Schema::hasColumn('barberias', 'tasa_bcv_modo')) {
                $table->string('tasa_bcv_modo')->default('auto')->comment('Modo de tasa: auto o manual');
            }
            if (!Schema::hasColumn('barberias', 'tasa_bcv_manual')) {
                $table->decimal('tasa_bcv_manual', 10, 2)->nullable()->comment('Valor manual de la tasa BCV');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barberias', function (Blueprint $table) {
            if (Schema::hasColumn('barberias', 'tasa_bcv_modo')) {
                $table->dropColumn('tasa_bcv_modo');
            }
            if (Schema::hasColumn('barberias', 'tasa_bcv_manual')) {
                $table->dropColumn('tasa_bcv_manual');
            }
        });
    }
};
