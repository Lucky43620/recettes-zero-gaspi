<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\FeatureLimitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureLimitServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeatureLimitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeatureLimitService();
    }

    public function test_free_user_cannot_exceed_pantry_limit()
    {
        $user = User::factory()->create();

        $this->assertTrue($this->service->canAdd($user, 'pantry_items', 4));
        $this->assertFalse($this->service->canAdd($user, 'pantry_items', 10));
    }

    public function test_premium_user_has_no_limits()
    {
        $user = User::factory()->create();
        $user->subscriptions()->create([
            'name' => 'default',
            'stripe_status' => 'active',
            'stripe_id' => 'sub_test',
            'stripe_price' => 'price_test',
        ]);

        $this->assertTrue($this->service->canAdd($user, 'pantry_items', 100));
        $this->assertTrue($this->service->canAdd($user, 'meal_plan_recipes', 100));
    }

    public function test_get_limit_message_for_free_user()
    {
        $message = $this->service->getLimitMessage('pantry_items');

        $this->assertStringContainsString('Limite atteinte', $message);
        $this->assertStringContainsString('10', $message);
    }

    public function test_meal_plan_limit_for_free_user()
    {
        $user = User::factory()->create();

        $this->assertTrue($this->service->canAdd($user, 'meal_plan_recipes', 2));
        $this->assertFalse($this->service->canAdd($user, 'meal_plan_recipes', 3));
    }

    public function test_collections_limit_for_free_user()
    {
        $user = User::factory()->create();

        $this->assertTrue($this->service->canAdd($user, 'collections', 1));
        $this->assertFalse($this->service->canAdd($user, 'collections', 2));
    }

    public function test_shopping_lists_limit_for_free_user()
    {
        $user = User::factory()->create();

        $this->assertTrue($this->service->canAdd($user, 'shopping_lists', 2));
        $this->assertFalse($this->service->canAdd($user, 'shopping_lists', 3));
    }

    public function test_unknown_feature_returns_false()
    {
        $user = User::factory()->create();

        $this->assertFalse($this->service->canAdd($user, 'unknown_feature', 0));
    }
}
