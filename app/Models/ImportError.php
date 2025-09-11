<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportError extends Model
{
    use HasFactory, SoftDeletes;

    public const TABLE = 'import_errors';

    protected $guarded = ['id'];  

    protected $attributes = [
        'deleted_token' => '',
    ];
    
    public function import()
    {
        return $this->belongsTo(Import::class, 'import_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
