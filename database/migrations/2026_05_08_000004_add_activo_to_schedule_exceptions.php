<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('schedule_exceptions', 'activo')) {
            Schema::table('schedule_exceptions', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('fechas_especificas');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('schedule_exceptions', 'activo')) {
            Schema::table('schedule_exceptions', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }
    }
};
