<?php

use Illuminate\Database\Seeder;
use App\PaymentType;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $payment_type = new PaymentType();
      $payment_type->name = 'Pago Mantenimiento';
      $payment_type->save();

      $payment_type = new PaymentType();
      $payment_type->name = 'Apartado Casa Club';
      $payment_type->save();

      $payment_type = new PaymentType();
      $payment_type->name = 'Fianza Casa Club';
      $payment_type->save();

      $payment_type = new PaymentType();
      $payment_type->name = 'Sanciones';
      $payment_type->save();

      $payment_type = new PaymentType();
      $payment_type->name = 'Recargo Moratorio';
      $payment_type->save();
    }
}
