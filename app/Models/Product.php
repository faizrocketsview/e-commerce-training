<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Formation\DataTable\WithTranslation;

class Product extends Model
{
    use HasFactory, SoftDeletes, WithTranslation;

    public const TABLE = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_token',
        'partition_created_at',
    ];

    // Declare translatable attributes
    public $translatable = ['name', 'description'];

    protected $attributes = [
        'deleted_token' => null,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->partition_created_at = now();
        });
    }

    public function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
