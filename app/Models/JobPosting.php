<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'grade',
        'location',
        'is_remote',
        'description',
        'min_experience',
        'required_skills',
        'required_languages',
        'custom_rules',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'required_skills' => 'array',
        'required_languages' => 'array',
        'custom_rules' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchRecord::class, 'job_posting_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_posting_id');
    }

    public function skillsRelation(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_posting_skills')->withPivot('is_required');
    }

    public function languagesRelation(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'job_posting_languages')->withPivot('min_level');
    }
}
