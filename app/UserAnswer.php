<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{

    protected $fillable = [
        'username',
        'survey_id',
        'question_id',
        'answer_type',
        'subject_id',
        'level',
        'answer',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function answerType()
    {
        return $this->belongsTo(AnswerType::class, 'answer_type', 'type');
    }
}
