<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Campaign;
use App\Models\Item;
use App\Services\ImageService;
use App\Services\TagService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly ImageService $imageService,
    ) {
    }

    public function index(Request $request)
    {
        $tag = $request->query('tag');

        $itemsQuery = Item::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'campaigns'])
            ->latest();

        if (is_string($tag) && $tag !== '') {
            $itemsQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        return view('items.index', [
            'items' => $itemsQuery->get(),
            'campaigns' => $request->user()->campaigns()->orderBy('name')->get(),
            'tag' => $tag,
        ]);
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->imageService->storePublic($request->file('image'), 'items');

        $item = Item::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($item, $request->user(), $validated['tags'] ?? []);

        return redirect()->route('items.index');
    }

    public function show(Item $item)
    {
        return redirect()->route('items.edit', $item);
    }

    public function edit(Item $item)
    {
        $this->authorizeEntity($item);
        $item->load('tags');

        return view('items.edit', [
            'item' => $item,
        ]);
    }

    public function update(StoreItemRequest $request, Item $item)
    {
        $this->authorizeEntity($item);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $this->imageService->deletePublic($item->image_path);
            $item->image_path = $this->imageService->storePublic($request->file('image'), 'items');
        }

        $item->name = $validated['name'];
        $item->description = $validated['description'] ?? null;
        $item->save();

        $this->tagService->syncTags($item, $request->user(), $validated['tags'] ?? []);

        return redirect()->route('items.index');
    }

    public function destroy(Item $item)
    {
        $this->authorizeEntity($item);

        $this->imageService->deletePublic($item->image_path);
        $item->tags()->detach();
        $item->campaigns()->detach();
        $item->delete();

        return redirect()->route('items.index');
    }

    public function attachToCampaign(Request $request, Item $item)
    {
        $this->authorizeEntity($item);

        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
        ]);

        $campaign = Campaign::query()
            ->where('id', $validated['campaign_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campaign->items()->syncWithoutDetaching([$item->id]);

        return back();
    }

    private function authorizeEntity(Item $item): void
    {
        abort_unless($item->user_id === auth()->id(), 404);
    }
}
