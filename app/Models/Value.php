<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'value';

    protected $fillable = [
        'text_de',
        'text_en',
        'type',
        'created_by_pair'
    ];
}
