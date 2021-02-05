<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Tenant;
use App\User;

class TenantCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant,User $user)
    {
        $this->tenant = $tenant;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-replay@kanulki.com')
                    ->markdown('email.tenantCreated')
                    ->subject('Confirmación de Registro Kanulki')
                    ->with([
                        'tenant' => $this->tenant,
                        'user' => $this->user,
                    ]);
    }
}
