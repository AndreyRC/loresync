<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AbilityController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
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
    Route::resource('abilities', AbilityController::class);
    Route::resource('characters', CharacterController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('items', ItemController::class);
    Route::resource('sessions', SessionController::class);
    Route::resource('maps', MapController::class);

    Route::post('characters/{character}/attach-to-campaign', [CharacterController::class, 'attachToCampaign'])->name('characters.attach-to-campaign');
    Route::post('characters/{character}/inventory', [CharacterController::class, 'addInventoryItem'])->name('characters.inventory.store');
    Route::delete('characters/{character}/inventory/{item}', [CharacterController::class, 'removeInventoryItem'])->name('characters.inventory.destroy');
    Route::post('characters/{character}/abilities', [CharacterController::class, 'addAbility'])->name('characters.abilities.store');
    Route::delete('characters/{character}/abilities/{ability}', [CharacterController::class, 'removeAbility'])->name('characters.abilities.destroy');
    Route::post('locations/{location}/attach-to-campaign', [LocationController::class, 'attachToCampaign'])->name('locations.attach-to-campaign');
    Route::post('items/{item}/attach-to-campaign', [ItemController::class, 'attachToCampaign'])->name('items.attach-to-campaign');

    Route::prefix('world-build')->name('world-build.')->group(function () {
        Route::get('/characters', fn () => redirect()->route('characters.index'))->name('characters');
        Route::get('/habilidades', fn () => redirect()->route('abilities.index'))->name('abilities');
        Route::get('/lugares', fn () => redirect()->route('locations.index'))->name('places');
        Route::get('/itens', fn () => redirect()->route('items.index'))->name('items');
    });
});

require __DIR__.'/auth.php';
