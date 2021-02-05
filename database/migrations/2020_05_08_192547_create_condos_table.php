<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->integer('number_houses')->default(0);
            $table->string('address')->default('');
            $table->integer('postal_code')->unsigned();
            $table->string('description')->default('');
            $table->string('country_id')->default('');
            $table->string('state')->default('');
            $table->string('city')->default('');
            $table->string('image')->default('');
            $table->string('rfc')->default('');
            $table->string('legal_name')->default('');
            $table->string('phone')->default('');
            $table->string('email')->default('');
            $table->string('county')->default('');
            $table->string('assembly_doc')->default('');
            $table->string('address_doc')->default('');
            $table->string('rfc_doc')->default('');
            $table->string('bank_statement')->default('');
            $table->string('club_house_gallery')->default('');

            $table->timestamps();

            // $table->string('topic')->after('message')->nullable();
            // $table->dropColumn('message');
            // $table->softDeletes();
            // $table->timestamps();

            // $table->primary('id');
            // $table->foreign('event_parking_type_id')->references('id')->on('event_parking_types');
            // $table->unsignedInteger('field_id');
            // $table->longText('text');
            // $table->string('event_id')->default('');
            // $table->decimal('platform_markup', 10, 2)->default(0.00);
            // \DB::table('ztw_transaction_type')->insert(['id' => 1, 'name' => 'RECHARGE_STRIPE']);
            // \DB::table('ztw_transaction_type')->insert(['id' => 2, 'name' => 'CHARGE_WALLET']);
            // \DB::table('ztw_transaction_type')->insert(['id' => 3, 'name' => 'CHARGE_CASH']);
        });
        
        \DB::table('condos')->insert([
            'id' => 1,
            'name' => 'Sevilla',
            'number_houses' => '30',
            'address' => 'PRIVADA SEVILLA MZ 20',
            'postal_code' => '77724',
            'description' => 'Condominio Sevilla',
            'country_id' => '139',
            'state' => 'Quintana Roo',
            'city' => 'PRIVADA SEVILLA MZ 20',
            'image' => '',
            'rfc' => 'PSE190617K36',
            'legal_name' => 'PRIVADA SEVILLA',
            'phone' => '(045) 9841321958',
            'email' => 'hugosalazarv@gmail.com',
            'county' => 'Luis Donaldo Colosio',
            'assembly_doc' => '',
            'address_doc' => '',
            'rfc_doc' => '',
            'bank_statement' => '',
            'club_house_gallery' => '',
        ]);
        \DB::table('condos')->insert([
            'id' => 2,
            'name' => 'Plumbago',
            'number_houses' => '30',
            'address' => 'Plumbago',
            'postal_code' => '77724',
            'description' => 'Condominio Plumbago',
            'country_id' => '139',
            'state' => '',
            'city' => '',
            'image' => '',
            'rfc' => '',
            'legal_name' => '',
            'phone' => '',
            'email' => '',
            'county' => '',
            'assembly_doc' => '',
            'address_doc' => '',
            'rfc_doc' => '',
            'bank_statement' => '',
            'club_house_gallery' => '',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condos');
    }
}
