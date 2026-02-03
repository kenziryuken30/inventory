<?php

namespace App\Models;

use App\Models\InvCategory;
use App\Models\InvSerialNumber;
use Illuminate\Database\Eloquent\Model;



class InvToolkit extends Model
{
    protected $table = 'inv_toolkit';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'toolkit_name',
        'category_id',
        'image'
    ];

    public function serialNumbers()
    {
        return $this->hasMany(InvSerialNumber::class, 'toolkit_id');
    }

    public function category()
    {
        return $this->belongsTo(InvCategory::class, 'category_id');
    }
}


