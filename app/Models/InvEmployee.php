<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvEmployee extends Model
{
    protected $table = 'inv_employee';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_id',
        'position_id',
        'full_name',
        'email',
        'no_tlpn'
    ];

    public function transactions()
    {
        return $this->hasMany(InvTransaction::class, 'employee_id');
    }
}
