<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'stok',
        'stok_minimum',
        'status'
    ];

    public function loanDetails()
    {
        return $this->hasMany(LoanDetail::class);
    }
}

