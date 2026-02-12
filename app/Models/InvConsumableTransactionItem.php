<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;  

class InvConsumableTransactionItem extends Model
{
    protected $table = 'inv_consumable_transaction_item';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'transaction_id',
        'consumable_id',
        'qty'
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
}
