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
        'employee_id',
        'date',
        'is_confirm'
    ];

    public function employee()
    {
        return $this->belongsTo(InvEmployee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(InvTransactionItem::class, 'transaction_id');
    }
}
