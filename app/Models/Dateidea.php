<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dateidea extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'dateidea';

    protected $fillable = [
        "text_de",
        "text_en",
        "title_de",
        "title_en",
        "description_de",
        "description_en",
        "type",
    ];

}
