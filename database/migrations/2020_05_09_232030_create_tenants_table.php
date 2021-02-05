<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->default(0);
            $table->integer('condo_id')->default(0);
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->string('name')->default('');
            $table->string('last_name')->default('');
            $table->string('address')->default('');
            $table->string('rfc')->default('');
            $table->string('phone')->default('');
            $table->string('image')->default('');
            $table->integer('country_id')->default(0);
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
        Schema::dropIfExists('tenants');
    }
}
