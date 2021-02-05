<?php

use Illuminate\Database\Seeder;
use App\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_method = new PaymentMethod();
		$payment_method->name = 'Depósito en banco';
		$payment_method->save();

		$payment_method = new PaymentMethod();
		$payment_method->name = 'Transferencia - SPEI';
		$payment_method->save();

		$payment_method = new PaymentMethod();
		$payment_method->name = 'Pago con Tarjeta de Débito y/o Crédito';
		$payment_method->save();

		$payment_method = new PaymentMethod();
		$payment_method->name = 'Pago con Conekta';
		$payment_method->save();
    }
}
