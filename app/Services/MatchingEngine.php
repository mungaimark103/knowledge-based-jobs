<?php

namespace App\Services;

use App\Models\JobPosting;
use App\Models\MatchingCriterion;
use App\Models\MatchingRule;
use App\Models\User;

class MatchingEngine
{
    /**
     * Score a candidate against a job using active weighted criteria
     * and rules. Returns the score plus a human-readable breakdown —
     * this breakdown is the "explanation facility" of the KBS.
     */
    public function evaluate(User $candidate, JobPosting $job): array
    {
        $candidate->loadMissing('candidateProfile');
        $profile = $candidate->candidateProfile;

        $criteria = MatchingCriterion::where('active', true)->get();
        $rules = MatchingRule::where('active', true)->get();

        $breakdown = [];
        $weightedScore = 0;
        $excluded = false;

        // Step 1: Apply weighted criteria (AHP-style scoring)
        foreach ($criteria as $criterion) {
            $rawScore = $this->scoreCriterion($criterion->key, $candidate, $job);
            $contribution = $rawScore * $criterion->weight;
            $weightedScore += $contribution;

            $breakdown[] = [
                'criterion' => $criterion->name,
                'key' => $criterion->key,
                'raw_score' => round($rawScore, 2),
                'weight' => (float) $criterion->weight,
                'contribution' => round($contribution, 2),
            ];
        }

        // Step 2: Apply global rule-based checks (IF-THEN logic)
        $explanations = [];
        foreach ($rules as $rule) {
            $result = $this->evaluateRule($rule, $candidate, $job);

            if ($result['triggered']) {
                $explanations[] = $result['explanation'];

                if ($rule->action === 'exclude') {
                    $excluded = true;
                } elseif ($rule->action === 'bonus') {
                    $weightedScore += 5; // Fixed bonus for specific rules
                }
            }
        }

        // Step 3: Apply agency-defined custom KBS rules for this job vacancy
        if (! empty($job->custom_rules) && is_array($job->custom_rules)) {
            foreach ($job->custom_rules as $cRule) {
                $rType = $cRule['type'] ?? 'SKILL';
                $rMode = $cRule['mode'] ?? 'WEIGHTED_FACTOR';
                $rVal = trim($cRule['value'] ?? '');
                $rTitle = trim($cRule['title'] ?? 'Custom Rule');
                $rWeight = (float) ($cRule['weight'] ?? 10);

                if (empty($rVal)) {
                    continue;
                }

                $ruleSatisfied = false;

                if ($rType === 'SKILL') {
                    $candSkills = collect($profile->skills ?? []);
                    $ruleSatisfied = $candSkills->contains(fn ($s) => stripos($s, $rVal) !== false);
                } elseif ($rType === 'LANGUAGE') {
                    $candLangs = collect($profile->languages ?? []);
                    $ruleSatisfied = $candLangs->contains(fn ($l) => stripos($l, $rVal) !== false);
                } elseif ($rType === 'EDUCATION') {
                    $ruleSatisfied = stripos($profile->education_level ?? '', $rVal) !== false;
                } elseif ($rType === 'EXPERIENCE') {
                    $ruleSatisfied = ($profile->years_experience ?? 0) >= (int) $rVal;
                } else {
                    $rawText = json_encode($profile->toArray());
                    $ruleSatisfied = stripos($rawText, $rVal) !== false;
                }

                if ($rMode === 'MANDATORY_KNOCKOUT' && ! $ruleSatisfied) {
                    $excluded = true;
                    $explanations[] = "Knockout Rule Triggered: Candidate failed mandatory rule '{$rTitle}' ({$rVal}).";
                } elseif ($ruleSatisfied) {
                    if ($rMode === 'RECOMMENDED_BONUS') {
                        $weightedScore += $rWeight;
                        $explanations[] = "KBS Bonus Awarded: Candidate satisfied rule '{$rTitle}' (+{$rWeight}%).";
                    } elseif ($rMode === 'WEIGHTED_FACTOR') {
                        $weightedScore += ($rWeight / 2);
                    }
                }
            }
        }

        $finalScore = min(100.00, max(0.00, round($weightedScore, 2)));

        return [
            'score' => $finalScore,
            'status' => $excluded ? 'excluded' : ($finalScore >= 70 ? 'recommended' : 'flagged'),
            'breakdown' => $breakdown,
            'explanations' => $explanations,
        ];
    }

    /**
     * Score a single criterion from 0–100.
     */
    protected function scoreCriterion(string $key, User $candidate, JobPosting $job): float
    {
        return match ($key) {
            'skill_match' => $this->skillMatchScore($candidate, $job),
            'experience' => $this->experienceScore($candidate, $job),
            'reliability' => (float) ($candidate->candidateProfile->reliability_score ?? 80),
            default => 0.0,
        };
    }

    protected function skillMatchScore(User $candidate, JobPosting $job): float
    {
        $profile = $candidate->candidateProfile;

        // Try normalized skills relation first, then array fallback
        if ($job->relationLoaded('skillsRelation') && $job->skillsRelation->isNotEmpty()) {
            $requiredSkills = $job->skillsRelation->pluck('name');
            $candidateSkills = $profile && $profile->relationLoaded('skillsRelation')
                ? $profile->skillsRelation->pluck('name')
                : collect($profile->skills ?? []);
        } else {
            $candidateSkills = collect($profile->skills ?? []);
            $requiredSkills = collect($job->required_skills ?? []);
        }

        if ($requiredSkills->isEmpty()) {
            return 100.0;
        }

        $matched = $requiredSkills->intersect($candidateSkills)->count();

        return round(($matched / $requiredSkills->count()) * 100, 2);
    }

    protected function experienceScore(User $candidate, JobPosting $job): float
    {
        $candidateYears = $candidate->candidateProfile->years_experience ?? 0;
        $requiredYears = $job->min_experience ?? 0;

        if ($requiredYears === 0) {
            return 100.0;
        }

        return min(100.0, round(($candidateYears / $requiredYears) * 100, 2));
    }

    /**
     * Evaluate a single IF-THEN rule against a candidate/job pair.
     */
    protected function evaluateRule(MatchingRule $rule, User $candidate, JobPosting $job): array
    {
        $profile = $candidate->candidateProfile;

        // Try extracting attribute from profile first, then job
        $actualValue = data_get($profile, $rule->field) ?? data_get($job, $rule->field);

        $targetValue = $rule->value;

        $triggered = match ($rule->operator) {
            '>=' => (float) $actualValue >= (float) $targetValue,
            '<=' => (float) $actualValue <= (float) $targetValue,
            '==' => (string) $actualValue === (string) $targetValue,
            'contains' => is_array($actualValue) && in_array($targetValue, $actualValue),
            default => false,
        };

        return [
            'triggered' => $triggered,
            'explanation' => $triggered
                ? str_replace(':value', (string) $targetValue, $rule->explanation_template)
                : null,
        ];
    }
}
