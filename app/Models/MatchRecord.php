<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRecord extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'candidate_id',
        'job_posting_id',
        'score',
        'breakdown',
        'explanations',
        'status',
    ];

    protected $casts = [
        'score' => 'float',
        'breakdown' => 'array',
        'explanations' => 'array',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }
}
