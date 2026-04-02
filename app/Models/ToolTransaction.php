<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvEmployee;

class ToolTransaction extends Model
{
    protected $table = 'inv_transaction';

    protected $fillable = [
        'transaction_code',
        'borrower_name',
        'client_name',
        'project',
        'purpose',
        'date',
        'is_confirm',
        'employee_id'
    ];

    protected $casts = [
        'date' => 'date',
        'is_confirm' => 'boolean',
    ];


    // 🔗 relasi ke detail tools
    public function items()
    {
        return $this->hasMany(ToolTransactionItem::class, 'transaction_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(InvEmployee::class, 'employee_id');
    }
}
