<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetSelections extends Model
{
    protected $fillable = [
        'bet_id', 'selection_id', 'selection_id', 'odds'
    ];

    public $timestamps = false;
}