<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvCategory;

class InvConsumable extends Model
{
    protected $table = 'inv_consumables';

    protected $fillable = [
        'name',
        'category_id',
        'stock',
        'minimum_stock',
        'unit',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(InvCategory::class, 'category_id', 'id');
    }

    public function transactionItems()
    {
        return $this->hasMany(
            InvConsumableTransactionItem::class,
            'consumable_id'
        );
    }
}

