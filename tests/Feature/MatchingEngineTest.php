<?php

use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Models\MatchingCriterion;
use App\Models\MatchingRule;
use App\Models\Organization;
use App\Models\User;
use App\Services\MatchingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('matching engine evaluates candidate score, breakdown, and explanations', function () {
    // 1. Create Organization & Job Posting
    $org = Organization::create([
        'name' => 'UNICEF',
        'code' => 'UNICEF',
        'org_type' => 'UN_AGENCY',
    ]);

    $job = JobPosting::create([
        'organization_id' => $org->id,
        'title' => 'M&E Officer (P-3)',
        'grade' => 'P-3',
        'location' => 'Geneva',
        'is_remote' => false,
        'description' => 'Monitoring & Evaluation role.',
        'min_experience' => 5,
        'required_skills' => ['M&E', 'PHP'],
    ]);

    // 2. Set Up Active Criteria & Rules
    MatchingCriterion::create(['name' => 'Skill Match', 'key' => 'skill_match', 'weight' => 0.50, 'active' => true]);
    MatchingCriterion::create(['name' => 'Experience', 'key' => 'experience', 'weight' => 0.50, 'active' => true]);

    MatchingRule::create([
        'name' => 'Minimum Experience Threshold',
        'field' => 'years_experience',
        'operator' => '>=',
        'value' => '5',
        'action' => 'flag',
        'explanation_template' => 'Meets experience threshold of :value years',
        'active' => true,
    ]);

    // 3. Create Candidate Profile
    $user = User::factory()->create();
    CandidateProfile::create([
        'user_id' => $user->id,
        'education_level' => 'Master',
        'skills' => ['M&E', 'PHP'],
        'years_experience' => 6,
        'reliability_score' => 90.0,
    ]);

    // 4. Evaluate via Matching Engine
    $engine = new MatchingEngine();
    $result = $engine->evaluate($user, $job);

    expect($result['score'])->toBe(100.0)
        ->and($result['status'])->toBe('recommended')
        ->and($result['breakdown'])->toHaveCount(2)
        ->and($result['explanations'])->toContain('Meets experience threshold of 5 years');
});

test('matching engine triggers exclusion rule when candidate fails mandatory check', function () {
    $org = Organization::create(['name' => 'Gov Agency', 'code' => 'GOV', 'org_type' => 'GOV_BODY']);
    $job = JobPosting::create([
        'organization_id' => $org->id,
        'title' => 'Senior Director',
        'grade' => 'GS-14',
        'location' => 'Washington DC',
        'is_remote' => false,
        'description' => 'Executive level post.',
        'min_experience' => 10,
        'required_skills' => ['Leadership'],
    ]);

    MatchingCriterion::create(['name' => 'Experience', 'key' => 'experience', 'weight' => 1.00, 'active' => true]);

    MatchingRule::create([
        'name' => 'Mandatory Experience Rule',
        'field' => 'years_experience',
        'operator' => '<=',
        'value' => '2',
        'action' => 'exclude',
        'explanation_template' => 'Candidate has insufficient experience (<= :value years)',
        'active' => true,
    ]);

    $user = User::factory()->create();
    CandidateProfile::create([
        'user_id' => $user->id,
        'skills' => ['Leadership'],
        'years_experience' => 1,
    ]);

    $engine = new MatchingEngine();
    $result = $engine->evaluate($user, $job);

    expect($result['status'])->toBe('excluded')
        ->and($result['explanations'])->toContain('Candidate has insufficient experience (<= 2 years)');
});
