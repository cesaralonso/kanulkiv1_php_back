<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PaymentMethod;

class TenantPayment extends Model
{
	use SoftDeletes;

    protected $table = "tenant_payments";

    protected $fillable = ['*']; 


    public function charges()
    {
        return $this->hasMany('App\TenantCharge','tenant_payment_id');
    }

    public function getPaymentMethod(){
    	return PaymentMethod::find($this->payment_method)->name;
    }
}
