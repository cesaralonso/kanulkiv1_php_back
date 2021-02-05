<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('house_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('house_id')->unsigned()->default(0);
            $table->integer('year')->unsigned()->default(0);
            $table->boolean('is_actual')->default(true);
            $table->decimal('m1', 12, 2)->default(0.00);
            $table->decimal('m2', 12, 2)->default(0.00);
            $table->decimal('m3', 12, 2)->default(0.00);
            $table->decimal('m4', 12, 2)->default(0.00);
            $table->decimal('m5', 12, 2)->default(0.00);
            $table->decimal('m6', 12, 2)->default(0.00);
            $table->decimal('m7', 12, 2)->default(0.00);
            $table->decimal('m8', 12, 2)->default(0.00);
            $table->decimal('m9', 12, 2)->default(0.00);
            $table->decimal('m10', 12, 2)->default(0.00);
            $table->decimal('m11', 12, 2)->default(0.00);
            $table->decimal('m12', 12, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('house_payments');
    }
}
