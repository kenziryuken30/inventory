<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvTransactionItem extends Model
{
    protected $table = 'inv_transaction_item';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'transaction_id',
        'toolkit_id',
        'serial_id',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(InvTransaction::class, 'transaction_id');
    }

    public function toolkit()
    {
        return $this->belongsTo(InvToolkit::class, 'toolkit_id');
    }
}
