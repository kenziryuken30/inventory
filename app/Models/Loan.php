<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'peminjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status'
    ];

    public function details()
    {
        return $this->hasMany(LoanDetail::class);
    }
}
