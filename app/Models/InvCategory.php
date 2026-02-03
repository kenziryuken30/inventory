<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvCategory extends Model
{
    protected $table = 'inv_category';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_name',
    ];

    public function toolkits()
    {
        return $this->hasMany(InvToolkit::class, 'category_id');
    }
}
