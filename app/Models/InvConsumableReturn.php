<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvConsumableReturn extends Model
{
    protected $table = 'inv_consumable_returns';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
    'id',
    'transaction_id',
    'employee_id',
    'date',
    'note'
    ];

    public function items()
    {
        return $this->hasMany(
            InvConsumableReturnItem::class,
            'return_id'
        );
    }

    public function transaction()
    {
        return $this->belongsTo(
            InvConsumableTransaction::class,
            'transaction_id'
        );
    }
}

