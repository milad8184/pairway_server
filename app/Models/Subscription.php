<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'subscription';

    protected $fillable = [
        'subscription_type',
        'status',
        'start_date',
        'end_date',
        'pair_id'
    ];

    // Prüfen, ob das Abonnement aktiv ist
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }
}
