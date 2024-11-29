<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoveLanguage extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'love_language';

    protected $fillable = [
        "text_de",
        "text_en",
        "name_de",
        "name_en",
    ];
}
