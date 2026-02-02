<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumables extends Model
{
    protected $table = 'inv_consumables';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category_id',
        'stock',
        'minimum_stock',
        'unit',
        'image'
    ];
}
