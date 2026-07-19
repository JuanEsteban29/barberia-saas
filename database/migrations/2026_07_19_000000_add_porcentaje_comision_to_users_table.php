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
        if (!Schema::hasColumn('users', 'porcentaje_comision')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('porcentaje_comision')->nullable()->comment('Porcentaje de comisión individual para el barbero');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'porcentaje_comision')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('porcentaje_comision');
            });
        }
    }
};
