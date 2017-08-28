<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Survey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'unit',
        'title',
        'is_subject',
        'start_date',
        'end_date',
        'student',
        'lecture',
        'employee',
        'academic_year',
        'semester',
    ];

    public function surveyObjective()
    {
        return $this->hasMany(SurveyObjective::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }

    public function userAnswer()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
