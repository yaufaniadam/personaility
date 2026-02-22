<?php

namespace Tests\Feature\Auth;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_claims_pending_assessment()
    {
        // Simulate a guest assessment created 30 minutes ago
        $assessment = Assessment::create([
            'user_id' => null,
            'guest_name' => 'Guest',
            'created_at' => now()->subMinutes(30),
        ]);

        session(['pending_assessment_id' => $assessment->id]);

        $abstractUser = \Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn('John Doe')
            ->shouldReceive('getEmail')
            ->andReturn('john@example.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $provider = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        // Should redirect to assessment result
        $response->assertRedirect(route('assessment.result', $assessment->id));
        $this->assertAuthenticated();

        $assessment->refresh();
        $this->assertNotNull($assessment->user_id);
        $this->assertEquals(User::where('email', 'john@example.com')->first()->id, $assessment->user_id);
    }

    public function test_google_callback_claims_assessment_older_than_one_hour_but_less_than_24()
    {
        // Simulate a guest assessment created 2 hours ago (would have failed before fix)
        $assessment = Assessment::create([
            'user_id' => null,
            'guest_name' => 'Guest',
            'created_at' => now()->subHours(2),
        ]);

        session(['pending_assessment_id' => $assessment->id]);

        $abstractUser = \Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn('Jane Doe')
            ->shouldReceive('getEmail')
            ->andReturn('jane@example.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $provider = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        // Should redirect to assessment result
        $response->assertRedirect(route('assessment.result', $assessment->id));

        $assessment->refresh();
        $this->assertNotNull($assessment->user_id);
    }

    public function test_google_callback_handles_already_claimed_by_same_user()
    {
        // Create user first
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => '12345',
        ]);

        // Assessment already claimed by this user
        $assessment = Assessment::create([
            'user_id' => $user->id,
            'guest_name' => 'Guest',
            'created_at' => now()->subMinutes(10),
        ]);

        session(['pending_assessment_id' => $assessment->id]);

        $abstractUser = \Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')
            ->andReturn('12345')
            ->shouldReceive('getName')
            ->andReturn('Existing User')
            ->shouldReceive('getEmail')
            ->andReturn('existing@example.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $provider = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        // Should redirect to assessment result because we own it
        $response->assertRedirect(route('assessment.result', $assessment->id));

        // Session should be cleared
        $this->assertFalse(session()->has('pending_assessment_id'));
    }
}
