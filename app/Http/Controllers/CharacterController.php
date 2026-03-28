<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCharacterRequest;
use App\Models\Campaign;
use App\Models\Ability;
use App\Models\Character;
use App\Models\Item;
use App\Models\User;
use App\Services\CharacterService;
use App\Services\ImageService;
use App\Services\TagService;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly ImageService $imageService,
        private readonly CharacterService $characterService,
    ) {
    }

    public function index(Request $request)
    {
        $tag = $request->query('tag');
        $status = $request->query('status');
        $type = $request->query('type');

        $charactersQuery = Character::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'campaigns', 'playerProfile.player'])
            ->latest();

        if (is_string($tag) && $tag !== '') {
            $charactersQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        if (is_string($status) && $status !== '') {
            $charactersQuery->where('status', $status);
        }

        if (is_string($type) && $type !== '') {
            $charactersQuery->where('type', $type);
        }

        return view('characters.index', [
            'characters' => $charactersQuery->get(),
            'campaigns' => $request->user()->campaigns()->orderBy('name')->get(),
            'tag' => $tag,
            'status' => $status,
            'type' => $type,
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    public function create(Request $request)
    {
        return view('characters.create', [
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
            'availableCharacters' => Character::query()
                ->where('user_id', $request->user()->id)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
            'availablePlayers' => User::query()->orderBy('name')->get(['id', 'name']),
            'availableCampaigns' => $request->user()->campaigns()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCharacterRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->imageService->storePublic($request->file('image'), 'characters');

        $character = $this->characterService->create($request->user(), [
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => ($validated['type'] ?? null) === Character::TYPE_NPC ? ($validated['status'] ?? null) : null,
            'type' => $validated['type'],
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($character, $request->user(), $validated['tags'] ?? []);
        $this->characterService->syncAttributes($character, $validated['attributes'] ?? []);
        $this->characterService->syncRelationships($character, $validated['relationships'] ?? []);

        if ($character->type === Character::TYPE_PLAYER) {
            $campaign = Campaign::query()
                ->where('id', $validated['campaign_id'])
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $player = User::query()->findOrFail($validated['player_user_id']);

            $this->characterService->convertToPlayer($character, $player, $campaign);
        }

        return redirect()->route('characters.index');
    }

    public function show(Character $character)
    {
        $this->authorizeEntity($character);

        $character->load([
            'tags',
            'characterAttributes',
            'outgoingRelationships.relatedCharacter',
            'items.itemAttributes',
            'abilities.abilityAttributes',
            'playerProfile.player',
            'playerProfile.campaign',
        ]);

        return view('characters.show', [
            'character' => $character,
            'availableItems' => Item::query()
                ->where('user_id', request()->user()->id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'availableAbilities' => Ability::query()
                ->where('user_id', request()->user()->id)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
        ]);
    }

    public function edit(Character $character)
    {
        $this->authorizeEntity($character);

        $character->load(['tags', 'characterAttributes', 'outgoingRelationships', 'playerProfile']);

        return view('characters.edit', [
            'character' => $character,
            'tags' => $character->tags->pluck('name')->all(),
            'availableTags' => request()->user()->tags()->orderBy('name')->pluck('name'),
            'availableCharacters' => Character::query()
                ->where('user_id', request()->user()->id)
                ->where('id', '!=', $character->id)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
            'availablePlayers' => User::query()->orderBy('name')->get(['id', 'name']),
            'availableCampaigns' => request()->user()->campaigns()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(StoreCharacterRequest $request, Character $character)
    {
        $this->authorizeEntity($character);

        $validated = $request->validated();

        $imagePath = $character->image_path;

        if ($request->hasFile('image')) {
            $this->imageService->deletePublic($character->image_path);
            $imagePath = $this->imageService->storePublic($request->file('image'), 'characters');
        }

        $this->characterService->update($character, [
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => ($validated['type'] ?? null) === Character::TYPE_NPC ? ($validated['status'] ?? null) : null,
            'type' => $validated['type'],
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($character, $request->user(), $validated['tags'] ?? []);
        $this->characterService->syncAttributes($character, $validated['attributes'] ?? []);
        $this->characterService->syncRelationships($character, $validated['relationships'] ?? []);

        if ($character->type === Character::TYPE_PLAYER) {
            $campaign = Campaign::query()
                ->where('id', $validated['campaign_id'])
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $player = User::query()->findOrFail($validated['player_user_id']);

            $this->characterService->convertToPlayer($character, $player, $campaign);
        }

        return redirect()->route('characters.index');
    }

    public function destroy(Character $character)
    {
        $this->authorizeEntity($character);

        $this->imageService->deletePublic($character->image_path);
        $character->tags()->detach();
        $character->campaigns()->detach();
        $character->items()->detach();
        $character->abilities()->detach();
        $character->delete();

        return redirect()->route('characters.index');
    }

    public function attachToCampaign(Request $request, Character $character)
    {
        $this->authorizeEntity($character);

        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
        ]);

        $campaign = Campaign::query()
            ->where('id', $validated['campaign_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campaign->characters()->syncWithoutDetaching([$character->id]);

        return back();
    }

    public function addInventoryItem(Request $request, Character $character)
    {
        $this->authorizeEntity($character);

        $validated = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $item = Item::query()
            ->where('id', $validated['item_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $this->characterService->addOrUpdateInventoryItem(
            $character,
            $item,
            (int) ($validated['quantity'] ?? 1),
            $validated['notes'] ?? null,
        );

        return back();
    }

    public function removeInventoryItem(Request $request, Character $character, Item $item)
    {
        $this->authorizeEntity($character);

        abort_unless($item->user_id === $request->user()->id, 404);

        $this->characterService->removeInventoryItem($character, $item);

        return back();
    }

    public function addAbility(Request $request, Character $character)
    {
        $this->authorizeEntity($character);

        $validated = $request->validate([
            'ability_id' => ['required', 'integer', 'exists:abilities,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $ability = Ability::query()
            ->where('id', $validated['ability_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $this->characterService->addOrUpdateAbility(
            $character,
            $ability,
            $validated['notes'] ?? null,
        );

        return back();
    }

    public function removeAbility(Request $request, Character $character, Ability $ability)
    {
        $this->authorizeEntity($character);

        abort_unless($ability->user_id === $request->user()->id, 404);

        $this->characterService->removeAbility($character, $ability);

        return back();
    }

    private function authorizeEntity(Character $character): void
    {
        abort_unless($character->user_id === auth()->id(), 404);
    }
}
