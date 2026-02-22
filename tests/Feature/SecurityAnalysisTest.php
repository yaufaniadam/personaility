<?php

namespace Tests\Feature;

use App\Models\AiInsight;
use App\Models\Assessment;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SecurityAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_does_not_expose_raw_assessment_models()
    {
        $user = User::factory()->create();

        $assessment = Assessment::create([
            'user_id' => $user->id,
            'openness_score' => 3.5,
            'conscientiousness_score' => 4.0,
            'extraversion_score' => 2.5,
            'agreeableness_score' => 3.0,
            'neuroticism_score' => 1.5,
            'completed_at' => now(),
            'version' => '1.0',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('latestAssessment', fn (Assert $json) => $json
                    ->where('id', $assessment->id)
                    ->has('openness_score')
                    ->has('conscientiousness_score')
                    // Ensure sensitive or irrelevant fields are NOT present
                    ->missing('user_id')
                    ->missing('updated_at')
                    ->etc()
                )
            );
    }

    public function test_psychologist_show_does_not_expose_sensitive_fields()
    {
        // Manually create psychologist since no factory exists
        $psychologist = new Psychologist();
        $psychologist->name = 'Dr. Test';
        $psychologist->str_number = 'STR-12345';
        $psychologist->city = 'Jakarta';
        $psychologist->province = 'DKI Jakarta';
        $psychologist->specialization = 'Clinical';
        $psychologist->gender = 'Laki-laki';
        $psychologist->active = true;
        $psychologist->verified_status = true;
        $psychologist->save();

        $this->get(route('psychologists.show', $psychologist))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Psychologists/Show')
                ->has('psychologist', fn (Assert $json) => $json
                    ->where('id', $psychologist->id)
                    ->where('name', 'Dr. Test')
                    // Ensure timestamps and internal flags are not exposed
                    ->missing('created_at')
                    ->missing('updated_at')
                    ->missing('active')
                    ->has('str_number')
                    ->etc()
                )
            );
    }

    public function test_assessment_result_does_not_expose_raw_models()
    {
        $user = User::factory()->create();
        $assessment = Assessment::create([
            'user_id' => $user->id,
            'completed_at' => now(),
            'version' => '1.0',
        ]);

        $insight = AiInsight::create([
            'assessment_id' => $assessment->id,
            'character_type' => 'Test Type',
            'core_strength' => 'Strength',
            'blind_spot' => 'Blind Spot',
            'stress_pattern' => 'Stress',
            'category_analysis' => [],
            'growth_suggestion' => [],
            'raw_prompt' => 'Sensitive Prompt Data',
            'raw_response' => '{"sensitive": "data"}',
            'model_version' => 'gpt-4',
        ]);

        $this->actingAs($user)
            ->get(route('assessment.result', $assessment))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Assessment/Result')
                ->has('assessment', fn (Assert $json) => $json
                    ->where('id', $assessment->id)
                    ->missing('user_id')
                    ->missing('created_at')
                    ->etc()
                )
                ->has('insight', fn (Assert $json) => $json
                    ->where('character_type', 'Test Type')
                    // Ensure raw data is not exposed
                    ->missing('raw_prompt')
                    ->missing('raw_response')
                    ->missing('model_version')
                    ->etc()
                )
            );
    }

    public function test_assessment_history_does_not_expose_raw_models()
    {
        $user = User::factory()->create();
        $assessment = Assessment::create([
            'user_id' => $user->id,
            'completed_at' => now(),
            'version' => '1.0',
        ]);

        $this->actingAs($user)
            ->get(route('assessment.history'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Assessment/History')
                ->has('assessments.0', fn (Assert $json) => $json
                    ->where('id', $assessment->id)
                    ->missing('user_id')
                    ->missing('created_at')
                    ->etc()
                )
            );
    }

    public function test_generate_insight_uses_atomic_lock()
    {
        $user = User::factory()->create();
        $assessment = Assessment::create([
            'user_id' => $user->id,
            'completed_at' => now(),
            'version' => '1.0',
        ]);

        // Mock Cache::lock to simulate lock already taken
        Cache::shouldReceive('lock')
            ->once()
            ->with("gen_insight_{$assessment->id}", 30)
            ->andReturnUsing(function () {
                $lock = \Mockery::mock(\Illuminate\Contracts\Cache\Lock::class);
                $lock->shouldReceive('get')->once()->andReturn(false);
                return $lock;
            });

        $this->actingAs($user)
            ->post(route('assessment.insight', $assessment))
            ->assertRedirect()
            ->assertSessionHas('error', 'Analisa sedang diproses. Mohon tunggu.');
    }
}
