<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumableTransactionItems extends Model
{
    protected $table = 'inv_consumable_transaction_items';
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'transaction_id',
        'consumable_id',
        'qty'
    ];

    public function consumable()
    {
        return $this->belongsTo(InvConsumables::class, 'consumable_id');
    }
}
