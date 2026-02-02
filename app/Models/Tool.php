<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\SerialNumber;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Tool extends Model
{
    use SoftDeletes;

    protected $table = 'inv_toolkit';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'toolkit_name',
        'category_id',
        'status',
        'image',
    ];

    /**
     * Default value saat create
     */
    protected $attributes = [
        'status' => 'tersedia',
    ];

    /**
     * Relasi ke kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * (Opsional) relasi ke serial number
     * kalau kamu pakai tabel inv_serial_number
     */
    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class, 'toolkit_id');
    }
}
