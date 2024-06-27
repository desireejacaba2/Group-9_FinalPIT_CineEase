<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeatsBookedToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'seats_booked')) {
                $table->integer('seats_booked')->default(1)->after('poster');
            }
            $table->string('status')->default('reserved')->after('seats_booked');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('status');
            if (Schema::hasColumn('bookings', 'seats_booked')) {
                $table->dropColumn('seats_booked');
            }
        });
    }
}
