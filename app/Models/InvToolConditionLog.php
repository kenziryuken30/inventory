<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvSerialNumber;

class InvToolConditionLog extends Model
{
    protected $table = 'inv_tool_condition_logs';

    protected $fillable = [
        'serial_id',
        'condition',
        'note',
    ];

    public function serial()
    {
        return $this->belongsTo(
            InvSerialNumber::class,
            'serial_id',
            'id'
        );
    }
}
