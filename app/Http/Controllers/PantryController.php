<?php

namespace App\Http\Controllers;

use App\Enums\StorageLocation;
use App\Http\Requests\StorePantryItemRequest;
use App\Http\Requests\UpdatePantryItemRequest;
use App\Http\Resources\PantryItemResource;
use App\Models\PantryItem;
use App\Models\Unit;
use App\Services\FeatureLimitService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PantryController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = $request->user()->pantryItems()
            ->with(['ingredient', 'unit'])
            ->orderBy('expiration_date', 'asc');

        if ($request->has('filter')) {
            $filter = $request->filter;
            if ($filter === 'expiring') {
                $query->expiringSoon();
            } elseif ($filter === 'expired') {
                $query->expired();
            }
        }

        if ($request->has('storage')) {
            $query->where('storage_location', $request->storage);
        }

        $items = PantryItemResource::collection($query->get());

        $stats = [
            'total' => $request->user()->pantryItems()->count(),
            'expiring_soon' => $request->user()->pantryItems()->expiringSoon()->count(),
            'expired' => $request->user()->pantryItems()->expired()->count(),
        ];

        $storageLocations = $request->user()->pantryItems()
            ->whereNotNull('storage_location')
            ->distinct()
            ->pluck('storage_location')
            ->values()
            ->all();

        return Inertia::render('Pantry/Index', [
            'items' => $items->resolve(),
            'stats' => $stats,
            'storageLocations' => StorageLocation::values(),
            'units' => Unit::all()->values()->all(),
            'isPremium' => $request->user()->isPremium(),
            'itemLimit' => $request->user()->isPremium() ? null : 10,
        ]);
    }

    public function store(StorePantryItemRequest $request, FeatureLimitService $limitService)
    {
        $user = $request->user();
        $currentCount = $user->pantryItems()->count();

        if (! $limitService->canAdd($user, 'pantry_items', $currentCount)) {
            return redirect()->back()->with('error', $limitService->getLimitMessage('pantry_items'));
        }

        $validated = $request->validated();

        $pantryItem = $user->pantryItems()->create($validated);

        return redirect()->back()->with('success', 'Article ajouté au garde-manger.');
    }

    public function update(UpdatePantryItemRequest $request, PantryItem $pantryItem)
    {
        $this->authorize('update', $pantryItem);

        $validated = $request->validated();

        $pantryItem->update($validated);

        return redirect()->back()->with('success', 'Article mis à jour.');
    }

    public function destroy(PantryItem $pantryItem)
    {
        $this->authorize('delete', $pantryItem);

        $pantryItem->delete();

        return redirect()->back()->with('success', 'Article supprimé du garde-manger.');
    }
}
