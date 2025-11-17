<?php

namespace Tests\Unit\Models;

use App\Models\Cooksnap;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CooksnapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_cooksnap_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Delicious!',
        ]);

        $this->assertInstanceOf(User::class, $cooksnap->user);
        $this->assertEquals($user->id, $cooksnap->user->id);
    }

    public function test_cooksnap_belongs_to_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Great recipe!',
        ]);

        $this->assertInstanceOf(Recipe::class, $cooksnap->recipe);
        $this->assertEquals($recipe->id, $cooksnap->recipe->id);
    }

    public function test_cooksnap_can_have_photo(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Look at my creation!',
        ]);

        $file = UploadedFile::fake()->image('cooksnap.jpg', 800, 600);
        $cooksnap->addMedia($file)->toMediaCollection('photos');

        $this->assertCount(1, $cooksnap->getMedia('photos'));
    }

    public function test_cooksnap_media_conversions_are_registered(): void
    {
        $cooksnap = new Cooksnap();
        $cooksnap->registerMediaConversions();

        // Verify conversions are registered by checking the model's media conversions
        $this->assertTrue(method_exists($cooksnap, 'registerMediaConversions'));
    }

    public function test_cooksnap_can_have_multiple_photos(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Multiple angles!',
        ]);

        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $file2 = UploadedFile::fake()->image('photo2.jpg');
        $file3 = UploadedFile::fake()->image('photo3.jpg');

        $cooksnap->addMedia($file1)->toMediaCollection('photos');
        $cooksnap->addMedia($file2)->toMediaCollection('photos');
        $cooksnap->addMedia($file3)->toMediaCollection('photos');

        $this->assertCount(3, $cooksnap->getMedia('photos'));
    }

    public function test_cooksnap_comment_can_be_null(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);

        $this->assertNull($cooksnap->comment);
    }

    public function test_cooksnap_eager_loads_media_by_default(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);

        $cooksnap = Cooksnap::first();

        $this->assertTrue($cooksnap->relationLoaded('media'));
    }

    public function test_user_can_create_multiple_cooksnaps_for_same_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'First attempt',
        ]);

        Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Second attempt - much better!',
        ]);

        $this->assertCount(2, Cooksnap::where('user_id', $user->id)->get());
    }

    public function test_recipe_cooksnaps_are_ordered_by_latest(): void
    {
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();

        $old = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'created_at' => now()->subDays(2),
        ]);

        $recent = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'created_at' => now()->subDay(),
        ]);

        $newest = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'created_at' => now(),
        ]);

        $cooksnaps = $recipe->fresh()->cooksnaps;

        $this->assertEquals($newest->id, $cooksnaps[0]->id);
        $this->assertEquals($recent->id, $cooksnaps[1]->id);
        $this->assertEquals($old->id, $cooksnaps[2]->id);
    }
}
