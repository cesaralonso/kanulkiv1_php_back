<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Tenant;
use App\TenantPayment;

class PaymentCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant,TenantPayment $payment,$date)
    {
        $this->tenant = $tenant;
        $this->payment = $payment;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('test@kanulki.com')
                    ->markdown('email.paymentCreated')
                    ->subject('Confirmación de Pago Kanulki')
                    ->with([
                        'tenant' => $this->tenant,
                        'payment' => $this->payment,
                        'date' => $this->date
                    ]);
    }
}
