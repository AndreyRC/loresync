<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNPCRequest;
use App\Models\Campaign;
use App\Models\NPC;
use App\Services\ImageService;
use App\Services\NPCService;
use App\Services\TagService;
use Illuminate\Http\Request;

class NPCController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly ImageService $imageService,
        private readonly NPCService $npcService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tag = $request->query('tag');
        $status = $request->query('status');

        $npcsQuery = NPC::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'campaigns'])
            ->latest();

        if (is_string($tag) && $tag !== '') {
            $npcsQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        if (is_string($status) && $status !== '') {
            $npcsQuery->where('status', $status);
        }

        return view('npcs.index', [
            'npcs' => $npcsQuery->get(),
            'campaigns' => $request->user()->campaigns()->orderBy('name')->get(),
            'tag' => $tag,
            'status' => $status,
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('npcs.create', [
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
            'availableNpcs' => NPC::query()
                ->where('user_id', $request->user()->id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNPCRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->imageService->storePublic($request->file('image'), 'npcs');

        $npc = $this->npcService->create($request->user(), [
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($npc, $request->user(), $validated['tags'] ?? []);
        $this->npcService->syncAttributes($npc, $validated['attributes'] ?? []);
        $this->npcService->syncRelationships($npc, $validated['relationships'] ?? []);

        return redirect()->route('npcs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(NPC $npc)
    {
        $this->authorizeEntity($npc);

        $npc->load(['tags', 'npcAttributes', 'outgoingRelationships.relatedNpc']);

        return view('npcs.show', [
            'npc' => $npc,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NPC $npc)
    {
        $this->authorizeEntity($npc);

        $npc->load(['tags', 'npcAttributes', 'outgoingRelationships']);

        return view('npcs.edit', [
            'npc' => $npc,
            'availableTags' => request()->user()->tags()->orderBy('name')->pluck('name'),
            'availableNpcs' => NPC::query()
                ->where('user_id', request()->user()->id)
                ->where('id', '!=', $npc->id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreNPCRequest $request, NPC $npc)
    {
        $this->authorizeEntity($npc);

        $validated = $request->validated();

        $imagePath = $npc->image_path;

        if ($request->hasFile('image')) {
            $this->imageService->deletePublic($npc->image_path);
            $imagePath = $this->imageService->storePublic($request->file('image'), 'npcs');
        }

        $this->npcService->update($npc, [
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($npc, $request->user(), $validated['tags'] ?? []);
        $this->npcService->syncAttributes($npc, $validated['attributes'] ?? []);
        $this->npcService->syncRelationships($npc, $validated['relationships'] ?? []);

        return redirect()->route('npcs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NPC $npc)
    {
        $this->authorizeEntity($npc);

        $this->imageService->deletePublic($npc->image_path);
        $npc->tags()->detach();
        $npc->campaigns()->detach();
        $npc->delete();

        return redirect()->route('npcs.index');
    }

    public function attachToCampaign(Request $request, NPC $npc)
    {
        $this->authorizeEntity($npc);

        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
        ]);

        $campaign = Campaign::query()
            ->where('id', $validated['campaign_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campaign->npcs()->syncWithoutDetaching([$npc->id]);

        return back();
    }

    private function authorizeEntity(NPC $npc): void
    {
        abort_unless($npc->user_id === auth()->id(), 404);
    }
}
