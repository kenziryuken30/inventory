<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumableTransaction extends Model
{
    protected $table = 'inv_consumable_transactions';
    protected $primaryKey = 'id';


    protected $fillable = [
        'id',
        'transaction_code',
        'borrower_name',
        'client',
        'project',
        'purpose',
        'date',
        'is_confirm'
    ];

    protected $casts = [
        'date' => 'date',
        'is_confirm' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(
            InvConsumableTransactionItem::class,
            'transaction_id'
        );
    }
}
