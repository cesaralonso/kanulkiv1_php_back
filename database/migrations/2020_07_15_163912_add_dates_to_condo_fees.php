<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesToCondoFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('condo_fees', function (Blueprint $table) {
            $table->string('last_payment_day')->default(10);
            $table->string('start_month')->default(1);
            $table->string('end_month')->default(12);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('condo_fees', function (Blueprint $table) {
            
        });
    }
}
