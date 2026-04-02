<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvEmployee extends Model
{
    protected $table = 'inv_employee';

    protected $primaryKey = 'id';

    public $incrementing = false; // karena id = EMP001 (string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_id',
        'position_id',
        'full_name',
        'id_number',
        'email',
        'no_tlpn',
        'photo',
        'player_id',
        'qr_contact',
        'is_claim',
        'is_exit'
    ];

    protected $casts = [
        'is_claim' => 'boolean',
        'is_exit' => 'boolean',
    ];

    // 🔗 relasi ke transaksi (optional tapi bagus)
    public function transactions()
    {
        return $this->hasMany(ToolTransaction::class, 'employee_id', 'id');
    }
}