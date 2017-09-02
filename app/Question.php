<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'survey_id',
        'question',
        'answer_type',
        'choices'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'id');
    }

    public function answerType()
    {
        return $this->belongsTo(AnswerType::class, 'answer_type', 'id');
    }

    public function userAnswer()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
