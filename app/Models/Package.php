<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'total_weight',
        'total_price',
        'courier_charge',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class);
    }
}
