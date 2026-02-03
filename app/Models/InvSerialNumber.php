<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvSerialNumber extends Model
{
    protected $table = 'inv_serial_number';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'toolkit_id',
        'serial_number',
        'status',     
        'condition',  
        'image'
    ];

    public function toolkit()
    {
        return $this->belongsTo(InvToolkit::class, 'toolkit_id');
    }
}
