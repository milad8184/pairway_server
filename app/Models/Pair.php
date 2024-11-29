<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'pair';
    protected $primaryKey = 'id';

    protected $fillable = [
        "uuid",
        "user1_id",
        "user2_id",
        "connectkey",
        "anniversary_date",
        "subscription_id",
        "created_at"
    ];
}
