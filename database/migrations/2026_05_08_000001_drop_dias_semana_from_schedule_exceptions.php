<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('schedule_exceptions', 'dias_semana')) {
            Schema::table('schedule_exceptions', function (Blueprint $table) {
                $table->dropColumn('dias_semana');
            });
        }
    }

    public function down()
    {
        Schema::table('schedule_exceptions', function (Blueprint $table) {
            $table->json('dias_semana')->nullable();
        });
    }
};
