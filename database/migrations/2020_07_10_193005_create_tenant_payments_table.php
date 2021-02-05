<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tenant_id')->unsigned()->default(0);
            $table->string('payment_description');
            $table->integer('year')->unsigned()->default(0);
            $table->boolean('month')->default(true);
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->integer('payment_type')->unsigned()->default(1);

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
        Schema::dropIfExists('tenant_payments');
    }
}
