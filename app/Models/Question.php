<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'question';

    protected $fillable = [
        "text_de",
        "text_en",
        "type",
    ];

}
