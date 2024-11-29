<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'answer';

    protected $fillable = [
        'user_id',
        'querstion_id',
        'answer_text'
    ];
}
