<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'organic_method_id',
    ];

    public function organicMethod()
    {
        return $this->belongsTo(OrganicMethod::class);
    }
} 