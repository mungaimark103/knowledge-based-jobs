<?php

use App\Models\MatchingRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('agency admin can create, update, toggle, and delete global IF-THEN rules', function () {
    $admin = User::factory()->create(['role' => 'agency_admin']);

    // 1. Create Rule
    $response = $this->actingAs($admin)->post('/admin/rules', [
        'name' => 'Security Clearance Requirement',
        'field' => 'is_verified',
        'operator' => '==',
        'value' => '1',
        'action' => 'bonus',
        'explanation_template' => 'Candidate holds official verification',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('matching_rules', [
        'name' => 'Security Clearance Requirement',
        'field' => 'is_verified',
        'value' => '1',
    ]);

    $rule = MatchingRule::where('name', 'Security Clearance Requirement')->first();
    expect($rule)->not->toBeNull();

    // 2. Update Rule
    $updateResponse = $this->actingAs($admin)->put("/admin/rules/{$rule->id}", [
        'name' => 'Updated Security Clearance',
        'field' => 'is_verified',
        'operator' => '==',
        'value' => '1',
        'action' => 'flag',
        'explanation_template' => 'Updated explanation template',
    ]);

    $updateResponse->assertRedirect();
    $this->assertDatabaseHas('matching_rules', [
        'id' => $rule->id,
        'name' => 'Updated Security Clearance',
        'action' => 'flag',
    ]);

    // 3. Toggle Rule Active Status
    $toggleResponse = $this->actingAs($admin)->patch("/admin/rules/{$rule->id}/toggle");
    $toggleResponse->assertRedirect();
    expect($rule->fresh()->active)->toBeFalse();

    // 4. Delete Rule
    $deleteResponse = $this->actingAs($admin)->delete("/admin/rules/{$rule->id}");
    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('matching_rules', [
        'id' => $rule->id,
    ]);
});
