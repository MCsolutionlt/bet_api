<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'player';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id', 'balance'
    ];

    public $timestamps = false;
}