<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'total_weight',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
