<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingRule extends Model
{
    use HasFactory;

    protected $table = 'matching_rules';

    protected $fillable = [
        'name',
        'field',
        'operator',
        'value',
        'action',
        'explanation_template',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
