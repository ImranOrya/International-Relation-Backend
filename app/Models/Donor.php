<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sway\Traits\InvalidatableToken;

class Donor extends Model
{

    use InvalidatableToken;
    protected $guarded = [];
}
