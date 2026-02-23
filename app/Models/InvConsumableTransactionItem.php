<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;  

class InvConsumableTransactionItem extends Model
{
    protected $table = 'inv_consumable_transaction_item';
    protected $primaryKey = 'id';

    protected $timestamp = false;

    protected $fillable = [
        'transaction_id',
        'consumable_id',
        'qty',
        'qty_return'
    ];

    public function transaction()
    {
        return $this->belongsTo(
            InvConsumableTransaction::class,
            'transaction_id'
        );
    }

    public function consumable()
    {
        return $this->belongsTo(
            InvConsumable::class,
            'consumable_id'
        );
    }

    public function getSisaAttribute()
    {
        return $this->qty - $this->qty_return;
    }

}
