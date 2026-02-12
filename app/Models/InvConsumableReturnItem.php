<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumableReturnItem extends Model
{
    protected $table = 'inv_consumable_return_items';

    protected $fillable = [
        'return_id','consumable_id','qty'
    ];

    public function consumable()
    {
        return $this->belongsTo(
            InvConsumable::class,
            'consumable_id'
        );
    }
}
