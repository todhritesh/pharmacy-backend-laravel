<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sell extends Model
{
    use HasFactory;
    function customer(){
        return $this->hasOne(User::class,'id','user_id');
    }

    function getCreatedAtAttrubute(){
        return Carbon::parse($this->attributes('created_at'))->format('j F, Y');
    }
}
