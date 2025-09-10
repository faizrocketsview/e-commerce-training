<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Formation\DataTable\WithTranslation;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, WithTranslation;

    protected $fillable = [
        'name',
        'type',
        'status',
        'slug',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $translatable = ['name'];

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
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
