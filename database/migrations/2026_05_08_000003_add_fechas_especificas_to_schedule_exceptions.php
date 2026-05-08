<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('schedule_exceptions', 'fechas_especificas')) {
            Schema::table('schedule_exceptions', function (Blueprint $table) {
                $table->json('fechas_especificas')->nullable()->after('end_time');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('schedule_exceptions', 'fechas_especificas')) {
            Schema::table('schedule_exceptions', function (Blueprint $table) {
                $table->dropColumn('fechas_especificas');
            });
        }
    }
};
