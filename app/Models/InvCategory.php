<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvCategory extends Model
{
    protected $table = 'inv_category';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}

