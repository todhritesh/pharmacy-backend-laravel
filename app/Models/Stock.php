<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $appends = ['label','image_link','custom_exp','custom_mfd'];

    protected $hidden = [
        'image',
        "created_at",
        "updated_at"
    ];

    public function addedBy(){
        return $this->hasOne(User::class,'id','user_id')->select("id",'name');
    }

    public function getCustomMfdAttribute(){
        return  Carbon::parse($this->attributes['mfd'])->diffForHumans();
    }

    public function getLabelAttribute(){
        return $this->name;
    }

    public function getCustomExpAttribute(){
        return  Carbon::parse($this->attributes['exp'])->diffForHumans();
    }

    public function getImageLinkAttribute(){
        return asset("/storage/stocks/".$this->image);
    }
}
