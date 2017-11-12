<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $table = 'bet';

    protected $fillable = [
        'stake_amount', 'created_at'
    ];

    public $timestamps = false;
}