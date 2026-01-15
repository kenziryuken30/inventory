<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    protected $fillable = [
        'loan_id',
        'item_id'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

