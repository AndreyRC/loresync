<?php

namespace Tests\Feature;

use App\Models\Ability;
use App\Models\Character;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbilitySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_abilities(): void
    {
        $this->get(route('abilities.index'))
            ->assertRedirect('/login');
    }

    public function test_user_can_create_ability_with_attributes_and_tags(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('abilities.store'), [
            'name' => 'Fireball',
            'description' => 'A basic fire spell.',
            'type' => 'spell',
            'tags' => ['fire', 'magic'],
            'attributes' => [
                ['key' => 'damage', 'value' => '1d6'],
                ['key' => 'mana_cost', 'value' => '10'],
            ],
        ]);

        $response->assertRedirect(route('abilities.index'));

        $ability = Ability::query()
            ->where('user_id', $user->id)
            ->where('name', 'Fireball')
            ->firstOrFail();

        $this->assertDatabaseHas('ability_attributes', [
            'ability_id' => $ability->id,
            'key' => 'damage',
            'value' => '1d6',
        ]);

        $tag = Tag::query()
            ->where('user_id', $user->id)
            ->where('name', 'fire')
            ->firstOrFail();

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_type' => Ability::class,
            'taggable_id' => $ability->id,
        ]);
    }

    public function test_user_can_update_ability_and_attributes_are_replaced(): void
    {
        $user = User::factory()->create();

        $ability = Ability::create([
            'user_id' => $user->id,
            'name' => 'Shield',
            'description' => null,
            'type' => 'spell',
            'image_path' => null,
        ]);

        $ability->abilityAttributes()->createMany([
            ['key' => 'duration', 'value' => '1 minute'],
        ]);

        $response = $this->actingAs($user)->put(route('abilities.update', $ability), [
            'name' => 'Shield',
            'description' => 'Protective barrier.',
            'type' => 'spell',
            'tags' => ['defense'],
            'attributes' => [
                ['key' => 'duration', 'value' => '10 minutes'],
                ['key' => 'cooldown', 'value' => '1 hour'],
            ],
        ]);

        $response->assertRedirect(route('abilities.index'));

        $this->assertDatabaseMissing('ability_attributes', [
            'ability_id' => $ability->id,
            'key' => 'duration',
            'value' => '1 minute',
        ]);

        $this->assertDatabaseHas('ability_attributes', [
            'ability_id' => $ability->id,
            'key' => 'duration',
            'value' => '10 minutes',
        ]);

        $this->assertDatabaseHas('ability_attributes', [
            'ability_id' => $ability->id,
            'key' => 'cooldown',
            'value' => '1 hour',
        ]);
    }

    public function test_user_can_attach_ability_to_character_with_notes(): void
    {
        $user = User::factory()->create();

        $character = Character::create([
            'user_id' => $user->id,
            'location_id' => null,
            'name' => 'Aria',
            'title' => null,
            'description' => null,
            'status' => null,
            'type' => Character::TYPE_PLAYER,
            'image_path' => null,
        ]);

        $ability = Ability::create([
            'user_id' => $user->id,
            'name' => 'Sneak',
            'description' => null,
            'type' => 'skill',
            'image_path' => null,
        ]);

        $response = $this->actingAs($user)->post(route('characters.abilities.store', $character), [
            'ability_id' => $ability->id,
            'notes' => 'Only usable in dim light.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('ability_character', [
            'character_id' => $character->id,
            'ability_id' => $ability->id,
            'notes' => 'Only usable in dim light.',
        ]);
    }
}
