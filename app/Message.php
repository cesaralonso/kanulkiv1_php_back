<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "messages";

    public function tenant(){
    	return $this->belongsTo('App\Tenant');
    }
}
