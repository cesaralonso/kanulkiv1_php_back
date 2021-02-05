<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TenantCharge extends Model
{

	use SoftDeletes;

    protected $table = "tenant_charges";

    protected $fillable = ['*']; 

    public function payment()
    {
        return $this->belongsTo('App\TenantPayment','tenant_payment_id');
    }

    public function getDate(){
    	$date = Carbon::parse($this->created_at);
    	return $date->isoFormat('DD')." ".$date->isoFormat('MMMM')." ".$date->isoFormat('YYYY');
    }
}
