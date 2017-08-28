<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    protected $fillable = [
        'type',
        'description',
        'created_by',
        'updated_by'
    ];
}
