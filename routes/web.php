<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NPCController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('campaigns', CampaignController::class);
    Route::resource('npcs', NPCController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('items', ItemController::class);
    Route::resource('sessions', SessionController::class);
    Route::resource('maps', MapController::class);

    Route::post('npcs/{npc}/attach-to-campaign', [NPCController::class, 'attachToCampaign'])->name('npcs.attach-to-campaign');
    Route::post('locations/{location}/attach-to-campaign', [LocationController::class, 'attachToCampaign'])->name('locations.attach-to-campaign');
    Route::post('items/{item}/attach-to-campaign', [ItemController::class, 'attachToCampaign'])->name('items.attach-to-campaign');

    Route::prefix('world-build')->name('world-build.')->group(function () {
        Route::get('/npcs', fn () => redirect()->route('npcs.index'))->name('npcs');
        Route::get('/lugares', fn () => redirect()->route('locations.index'))->name('places');
        Route::get('/itens', fn () => redirect()->route('items.index'))->name('items');
    });
});

require __DIR__.'/auth.php';
