<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbilityRequest;
use App\Models\Ability;
use App\Services\AbilityService;
use App\Services\ImageService;
use App\Services\TagService;
use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly ImageService $imageService,
        private readonly AbilityService $abilityService,
    ) {
    }

    public function index(Request $request)
    {
        $tag = $request->query('tag');
        $type = $request->query('type');

        $abilitiesQuery = Ability::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'abilityAttributes'])
            ->latest();

        if (is_string($tag) && $tag !== '') {
            $abilitiesQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        if (is_string($type) && $type !== '') {
            $abilitiesQuery->where('type', $type);
        }

        return view('abilities.index', [
            'abilities' => $abilitiesQuery->get(),
            'tag' => $tag,
            'type' => $type,
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
            'availableTypes' => Ability::query()
                ->where('user_id', $request->user()->id)
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type'),
        ]);
    }

    public function create(Request $request)
    {
        return view('abilities.create', [
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    public function store(StoreAbilityRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->imageService->storePublic($request->file('image'), 'abilities');

        $ability = $this->abilityService->create($request->user(), [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($ability, $request->user(), $validated['tags'] ?? []);
        $this->abilityService->syncAttributes($ability, $validated['attributes'] ?? []);

        return redirect()->route('abilities.index');
    }

    public function show(Ability $ability)
    {
        return redirect()->route('abilities.edit', $ability);
    }

    public function edit(Ability $ability)
    {
        $this->authorizeEntity($ability);
        $ability->load(['tags', 'abilityAttributes']);

        return view('abilities.edit', [
            'ability' => $ability,
            'tags' => $ability->tags->pluck('name')->all(),
            'availableTags' => request()->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    public function update(StoreAbilityRequest $request, Ability $ability)
    {
        $this->authorizeEntity($ability);

        $validated = $request->validated();

        $imagePath = $ability->image_path;

        if ($request->hasFile('image')) {
            $this->imageService->deletePublic($ability->image_path);
            $imagePath = $this->imageService->storePublic($request->file('image'), 'abilities');
        }

        $this->abilityService->update($ability, [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($ability, $request->user(), $validated['tags'] ?? []);
        $this->abilityService->syncAttributes($ability, $validated['attributes'] ?? []);

        return redirect()->route('abilities.index');
    }

    public function destroy(Ability $ability)
    {
        $this->authorizeEntity($ability);

        $this->imageService->deletePublic($ability->image_path);
        $ability->tags()->detach();
        $ability->characters()->detach();
        $ability->delete();

        return redirect()->route('abilities.index');
    }

    private function authorizeEntity(Ability $ability): void
    {
        abort_unless($ability->user_id === auth()->id(), 404);
    }
}
