<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use HasFactory, SoftDeletes;

    public const TABLE = 'imports';

    protected $guarded = ['id'];  

    protected $attributes = [
        'deleted_token' => '',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public const STATUSES = [
        'new' => 'new',
        'progressing' => 'progressing',
        'completed' => 'completed',
        'failed' => 'failed',
    ];

    public function importErrors()
    {
        return $this->hasMany(ImportError::class, 'import_id');
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
