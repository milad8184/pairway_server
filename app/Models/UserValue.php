<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserValue extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'user_value';

    protected $fillable = [
        'value_id',
        'user_id',
        'type'
    ];
}
