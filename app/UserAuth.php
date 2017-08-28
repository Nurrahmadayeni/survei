<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAuth extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'username',
        'auth_type',
        'unit',
        'created_by',
        'updated_by'
    ];

    public function auths()
    {
        return $this->belongsTo(Auth::class, 'auth_type', 'type');
    }
}
