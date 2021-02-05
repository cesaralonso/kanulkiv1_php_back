<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TenantCharge;

class Tenant extends Model
{
    protected $table = 'tenants';

    // public function users()
    // {
    //     return $this->belongsToMany('App\User')->withTimestamps();
    // }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function charges (){
    	return $this->hasMany('App\TenantCharge','tenant_id');
    }

    public function getPendingCharges(){
    	return TenantCharge::where('tenant_id',$this->id)->where('paid',0)->get();
    }

    public function getBalance(){
        $balance = TenantCharge::where('tenant_id',$this->id)->where('paid',0)->get()->sum('amount');
        $this->balance = $balance;
        $this->save();
    	return $balance;
    }

    public function condo(){
        return $this->belongsTo('App\Condo');
    }

    public function house(){
        return $this->belongsTo('App\House');
    }

    public function messages(){
        return $this->hasMany('App\Message');
    }
}
