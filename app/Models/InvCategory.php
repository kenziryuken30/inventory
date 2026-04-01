<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvCategory extends Model
{
    use HasFactory;

    protected $table = 'inv_category';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_name',
    ];

    public function consumables()
    {
        return $this->hasMany(InvConsumable::class, 'category_id', 'id');
    }

    public function toolkits()
    {
        return $this->hasMany(InvToolkit::class, 'category_id', 'id');
    }

    public static function generateId(): string
    {
        $last = self::orderBy('id', 'desc')->first();

        if (!$last) {
            return 'CAT-01';
        }

        $lastNumber = (int) str_replace('CAT-', '', $last->id);
        $nextNumber = $lastNumber + 1;

        return 'CAT-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}