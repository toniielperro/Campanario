<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedule_exceptions', function (Blueprint $table) {
            if (Schema::hasColumn('schedule_exceptions', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('schedule_exceptions', 'weekday')) {
                $table->dropColumn('weekday');
            }
            if (Schema::hasColumn('schedule_exceptions', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }

    public function down()
    {
        Schema::table('schedule_exceptions', function (Blueprint $table) {
            if (! Schema::hasColumn('schedule_exceptions', 'date')) {
                $table->date('date')->nullable();
            }
            if (! Schema::hasColumn('schedule_exceptions', 'weekday')) {
                $table->string('weekday')->nullable();
            }
            if (! Schema::hasColumn('schedule_exceptions', 'reason')) {
                $table->string('reason')->nullable();
            }
        });
    }
};
