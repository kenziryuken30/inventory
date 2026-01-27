<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumable extends Model
{
    protected $table = 'inv_consumable';

    protected $fillable = [
        'name',
        'category_id',
        'stock',
        'unit',
        'description'
    ];
}
