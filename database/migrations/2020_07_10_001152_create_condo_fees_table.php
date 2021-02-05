<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondoFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condo_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('year')->unsigned()->default(0);
            $table->boolean('is_actual')->default(true);
            $table->decimal('maintenance', 12, 2)->default(0.00);
            $table->decimal('interest', 12, 2)->default(0.00);
            $table->decimal('club_house_morning', 12, 2)->default(0.00);
            $table->decimal('club_house_evening', 12, 2)->default(0.00);
            $table->integer('condo_id')->default(0);
            
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
        Schema::dropIfExists('condo_fees');
    }
}
