<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingCriterion extends Model
{
    use HasFactory;

    protected $table = 'matching_criteria';

    protected $fillable = [
        'name',
        'key',
        'weight',
        'description',
        'active',
    ];

    protected $casts = [
        'weight' => 'float',
        'active' => 'boolean',
    ];
}
