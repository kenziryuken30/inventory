<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvTransaction extends Model
{
    protected $table = 'inv_transaction';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'borrower_name',
        'client_name',
        'project',
        'purpose',
        'date',
        'is_confirm'
    ];

    protected $casts = [
        'date' => 'date',
        'is_confirm' => 'boolean',
    ];


    // 🔗 relasi ke detail tools
    public function items()
    {
        return $this->hasMany(InvTransactionItem::class, 'transaction_id');
    }
}
