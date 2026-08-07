<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'education_level',
        'summary',
        'skills',
        'years_experience',
        'field_experience_months',
        'reliability_score',
        'languages',
        'work_history',
        'education_history',
        'references_list',
        'resume_path',
        'resume_filename',
        'is_verified',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'work_history' => 'array',
        'education_history' => 'array',
        'references_list' => 'array',
        'reliability_score' => 'float',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skillsRelation(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills');
    }

    public function languagesRelation(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'candidate_languages')->withPivot('proficiency_level');
    }
}
