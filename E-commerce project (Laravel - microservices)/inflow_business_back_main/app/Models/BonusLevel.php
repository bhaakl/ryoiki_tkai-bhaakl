<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusLevel extends Model
{
    protected $fillable = [
        'name',
        'threshold',
        'bonus_active_days',
        'percent',
        'bonus_pay_percent',
    ];
}
