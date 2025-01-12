<?php

namespace App\Models;

use App\Traits\template\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Ngo extends Model
{
    //


     use HasFactory, Notifiable, HasApiTokens, Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


       protected $hidden = [
        'password',
        'remember_token',
    ];
    


       public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }
    
    public function ngoTrans()
    {
        return $this->hasMany(NgoTran::class);
    }

    public function ngoType()
    {
        return $this->belongsTo(NgoType::class, 'ngo_type_id');
    }

    public function ngoStatus()
    {
        return $this->hasOne(NgoStatus::class, 'ngo_id', 'id');
    }


    public function agreement()
    {
        return $this->hasOne(Agreement::class, 'ngo_id');
    }
}
