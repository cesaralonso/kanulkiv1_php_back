<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Condo extends Model
{
    protected $table = 'condos';

    public function tenants(){
    	return $this->hasMany('App\Tenant');
    }

    public function messages(){
    	return $this->hasManyThrough('App\Message', 'App\Tenant');
    }
}
