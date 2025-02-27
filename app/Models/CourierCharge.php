<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'min_weight',
        'max_weight',
        'charge',
    ];
}
