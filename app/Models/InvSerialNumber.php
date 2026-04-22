<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvToolkit;
use App\Models\InvToolConditionLog;

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
        'image'
    ];

    // relasi ke toolkit
    public function toolkit()
    {
        return $this->belongsTo(InvToolkit::class, 'toolkit_id');
    }

    // semua riwayat kondisi
    public function conditionLogs()
    {
        return $this->hasMany(InvToolConditionLog::class, 'serial_id');
    }

    // kondisi terakhir
    public function latestCondition()
    {
        return $this->hasOne(InvToolConditionLog::class, 'serial_id')
                    ->latestOfMany();
    }

    public function latestTransaction()
{
    return $this->hasOne(\App\Models\ToolTransactionItem::class, 'serial_id')
        ->latestOfMany(); 
}

public function getStatusLabelAttribute()
{
    if ($this->latestTransaction?->status === 'DIPINJAM') {
        return 'dipinjam';
    }

    if ($this->latestTransaction?->status === 'PENDING') {
        return 'pending';
    }

    if (in_array($this->latestCondition?->condition, ['rusak', 'maintenance'])) {
        return 'tidak tersedia';
    }

    return 'tersedia';
}
}

