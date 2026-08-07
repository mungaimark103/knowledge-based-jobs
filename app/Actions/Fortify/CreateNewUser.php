<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'role' => ['sometimes', 'string', 'in:candidate,employer'],
            'password' => $this->passwordRules(),
        ])->validate();

        $role = $input['role'] ?? 'candidate';

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $role,
            'password' => $input['password'],
        ]);

        if ($role === 'candidate') {
            \App\Models\CandidateProfile::create([
                'user_id' => $user->id,
                'education_level' => null,
                'years_experience' => 0,
                'field_experience_months' => 0,
                'reliability_score' => null,
                'skills' => [],
                'languages' => ['English'],
            ]);
        } elseif ($role === 'employer') {
            $orgName = ! empty($input['organization_name']) ? $input['organization_name'] : ($user->name . ' Organization');
            $rawCode = ! empty($input['organization_code']) ? $input['organization_code'] : $user->name;
            $orgCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $rawCode), 0, 6)) . rand(10, 99);
            $orgType = ! empty($input['organization_type']) ? $input['organization_type'] : 'PRIVATE_COMPANY';

            \App\Models\Organization::create([
                'user_id' => $user->id,
                'name' => $orgName,
                'code' => $orgCode,
                'org_type' => $orgType,
            ]);
        }

        return $user;
    }
}
