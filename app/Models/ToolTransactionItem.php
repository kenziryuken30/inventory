<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolTransactionItem extends Model
{
    protected $table = 'inv_transaction_item';

    protected $fillable = [
        'transaction_id',
        'toolkit_id',
        'serial_id',
        'status',
        'condition',
        'note',
        'return_date',
    ];

    protected $casts = [
        'return_date' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(ToolTransaction::class, 'transaction_id');
    }

    public function toolkit()
    {
        return $this->belongsTo(InvToolkit::class, 'toolkit_id');
    }

    public function serial()
    {
        return $this->belongsTo(InvSerialNumber::class, 'serial_id');
    }
}