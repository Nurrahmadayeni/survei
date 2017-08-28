<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerType extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'type',
    ];
}
