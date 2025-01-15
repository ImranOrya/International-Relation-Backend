<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class UserDetail extends Model
{
    //


      protected $guarded = [];

    protected $casts = [
        'grant_permission' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */



    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', "id");
    }
    public function job()
    {
        return $this->belongsTo(ModelJob::class, 'job_id', "id");
    }

    public function destinationThrough()
    {
        return $this->hasOneThrough(
            Translate::class,
            Destination::class,
            'id',
            'translable_id',
            'destination_id',
            'id'
        );
    }

    public function jobThrough()
    {
        return $this->hasOneThrough(
            Translate::class,
            ModelJob::class,
            'id',
            'translable_id',
            'job_id',
            'id'
        );
    }

    public function userDetailTran(){

        return $this->hasOne(UserDetailTran::class)->where('language_name',App::getLocale());
    }



}
