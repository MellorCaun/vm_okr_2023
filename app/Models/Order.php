<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'order_receipt',
        'status'
    ];

    public static function find($id) {
        return static::where('id', $id)->first();
    }

    public function orderItems() {
        return $this->hasMany(OrderItems::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
