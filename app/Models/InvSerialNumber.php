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
    protected static function booted()
{
    static::created(function ($serial) {
        InvToolConditionLog::create([
            'serial_id' => $serial->id,
            'condition' => 'baik',
            'note' => 'Kondisi awal saat input alat',
        ]);
    });
}


    // relasi ke toolkit
    public function toolkit()
    {
        return $this->belongsTo(InvToolkit::class, 'toolkit_id');
    }

    // semua riwayat kondisi
    public function conditionLogs()
    {
        return $this->hasMany(
            InvToolConditionLog::class,
            'serial_id',
            'id'
        );
    }

    // kondisi TERAKHIR (yang dipakai di UI)
    public function latestCondition()
    {
        return $this->hasOne(
            InvToolConditionLog::class,
            'serial_id',
            'id'
        )->latestOfMany();
    }
}
