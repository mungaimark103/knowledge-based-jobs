<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'candidate_id',
        'job_title_snapshot',
        'organization_name_snapshot',
        'status',
        'screening_answers',
        'education_data',
        'work_history_data',
        'references_data',
        'motivational_statement',
        'integrity_accepted',
        'ai_declaration_accepted',
        'applied_at',
    ];

    protected $casts = [
        'screening_answers' => 'array',
        'education_data' => 'array',
        'work_history_data' => 'array',
        'references_data' => 'array',
        'integrity_accepted' => 'boolean',
        'ai_declaration_accepted' => 'boolean',
        'applied_at' => 'datetime',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}
