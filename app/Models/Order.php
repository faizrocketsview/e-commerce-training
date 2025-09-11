<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const TABLE = 'orders';

    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'total_price',
        'attachments',
        'placed_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_token',
        'partition_created_at',
    ];

    protected $attributes = [
        'deleted_token' => null,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->partition_created_at = now();
            if (empty($model->placed_at)) {
                $model->placed_at = now();
            }
        });
    }

    public function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
