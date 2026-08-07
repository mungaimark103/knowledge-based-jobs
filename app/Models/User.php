<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'google_id', 'password', 'role', 'agency_sub_role'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function candidateProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Organization::class);
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isAgencyAdmin(): bool
    {
        return $this->role === 'agency_admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'agency_admin' && ($this->agency_sub_role === 'super_admin' || empty($this->agency_sub_role));
    }

    public function isVerificationOfficer(): bool
    {
        return $this->role === 'agency_admin' && $this->agency_sub_role === 'verification_officer';
    }

    public function isComplianceAuditor(): bool
    {
        return $this->role === 'agency_admin' && $this->agency_sub_role === 'compliance_auditor';
    }
}
