<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $guarded = [];

    public function ngo()
    {
        return $this->belongsTo(Ngo::class, 'ngo_id', 'id');
    }

    public function userStatusType(){
        return $this->belongsTo(StatusType::class);
    }
}
