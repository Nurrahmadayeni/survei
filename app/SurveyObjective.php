<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyObjective extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'objective',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
