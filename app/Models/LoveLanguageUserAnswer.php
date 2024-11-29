<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoveLanguageUserAnswer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'love_language_user_answer';

    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id'
    ];
}
