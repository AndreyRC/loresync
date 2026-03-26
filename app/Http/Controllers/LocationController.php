<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Campaign;
use App\Models\Location;
use App\Services\ImageService;
use App\Services\TagService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
        private readonly ImageService $imageService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tag = $request->query('tag');

        $locationsQuery = Location::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'campaigns'])
            ->latest();

        if (is_string($tag) && $tag !== '') {
            $locationsQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        return view('locations.index', [
            'locations' => $locationsQuery->get(),
            'campaigns' => $request->user()->campaigns()->orderBy('name')->get(),
            'tag' => $tag,
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('locations.create', [
            'availableTags' => $request->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $this->imageService->storePublic($request->file('image'), 'locations');

        $location = Location::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->tagService->syncTags($location, $request->user(), $validated['tags'] ?? []);

        return redirect()->route('locations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        return redirect()->route('locations.edit', $location);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $this->authorizeEntity($location);

        $location->load('tags');

        return view('locations.edit', [
            'location' => $location,
            'availableTags' => request()->user()->tags()->orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreLocationRequest $request, Location $location)
    {
        $this->authorizeEntity($location);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $this->imageService->deletePublic($location->image_path);
            $location->image_path = $this->imageService->storePublic($request->file('image'), 'locations');
        }

        $location->name = $validated['name'];
        $location->description = $validated['description'] ?? null;
        $location->save();

        $this->tagService->syncTags($location, $request->user(), $validated['tags'] ?? []);

        return redirect()->route('locations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $this->authorizeEntity($location);

        $this->imageService->deletePublic($location->image_path);
        $location->tags()->detach();
        $location->campaigns()->detach();
        $location->delete();

        return redirect()->route('locations.index');
    }

    public function attachToCampaign(Request $request, Location $location)
    {
        $this->authorizeEntity($location);

        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
        ]);

        $campaign = Campaign::query()
            ->where('id', $validated['campaign_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campaign->locations()->syncWithoutDetaching([$location->id]);

        return back();
    }

    private function authorizeEntity(Location $location): void
    {
        abort_unless($location->user_id === auth()->id(), 404);
    }
}
