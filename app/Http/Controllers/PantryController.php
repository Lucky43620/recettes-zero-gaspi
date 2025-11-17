<?php

namespace App\Http\Controllers;

use App\Enums\StorageLocation;
use App\Http\Requests\StorePantryItemRequest;
use App\Http\Requests\UpdatePantryItemRequest;
use App\Http\Resources\PantryItemResource;
use App\Models\PantryItem;
use App\Models\Unit;
use App\Services\FeatureLimitService;
use App\Services\UnitService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PantryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private UnitService $unitService
    ) {}

    public function index(Request $request)
    {
        $allItems = $request->user()->pantryItems()
            ->with(['ingredient', 'unit'])
            ->orderBy('expiration_date', 'asc')
            ->get();

        if ($request->has('filter')) {
            $filter = $request->filter;
            if ($filter === 'expiring') {
                $filteredItems = $allItems->filter(fn($item) => $item->isExpiringSoon());
            } elseif ($filter === 'expired') {
                $filteredItems = $allItems->filter(fn($item) => $item->isExpired());
            } else {
                $filteredItems = $allItems;
            }
        } else {
            $filteredItems = $allItems;
        }

        if ($request->has('storage')) {
            $filteredItems = $filteredItems->where('storage_location', $request->storage);
        }

        $items = PantryItemResource::collection($filteredItems);

        $stats = [
            'total' => $allItems->count(),
            'expiring_soon' => $allItems->filter(fn($item) => $item->isExpiringSoon())->count(),
            'expired' => $allItems->filter(fn($item) => $item->isExpired())->count(),
        ];

        $storageLocations = $allItems
            ->whereNotNull('storage_location')
            ->pluck('storage_location')
            ->unique()
            ->values()
            ->all();

        return Inertia::render('Pantry/Index', [
            'items' => $items->resolve(),
            'stats' => $stats,
            'storageLocations' => StorageLocation::values(),
            'units' => $this->unitService->getAllUnits()->values()->all(),
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
