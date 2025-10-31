<?php

namespace App\Http\Controllers;

use App\Models\PantryItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PantryController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->pantryItems()
            ->with(['ingredient', 'unit'])
            ->orderBy('expiration_date', 'asc');

        if ($request->has('filter')) {
            $filter = $request->filter;
            if ($filter === 'expiring') {
                $query->where('expiration_date', '<=', now()->addDays(3))
                      ->where('expiration_date', '>=', now());
            } elseif ($filter === 'expired') {
                $query->where('expiration_date', '<', now());
            }
        }

        if ($request->has('storage')) {
            $query->where('storage_location', $request->storage);
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'ingredient' => [
                    'id' => $item->ingredient->id,
                    'name' => $item->ingredient->name,
                    'image_url' => $item->ingredient->image_url,
                ],
                'quantity' => $item->quantity,
                'unit' => [
                    'code' => $item->unit->code,
                    'name' => $item->unit->name,
                ],
                'expiration_date' => $item->expiration_date?->format('Y-m-d'),
                'storage_location' => $item->storage_location,
                'opened' => $item->opened,
                'is_expired' => $item->isExpired(),
                'is_expiring_soon' => $item->isExpiringSoon(),
                'days_until_expiration' => $item->daysUntilExpiration(),
            ];
        });

        $stats = [
            'total' => $request->user()->pantryItems()->count(),
            'expiring_soon' => $request->user()->pantryItems()
                ->where('expiration_date', '<=', now()->addDays(3))
                ->where('expiration_date', '>=', now())
                ->count(),
            'expired' => $request->user()->pantryItems()
                ->where('expiration_date', '<', now())
                ->count(),
        ];

        $storageLocations = $request->user()->pantryItems()
            ->whereNotNull('storage_location')
            ->distinct()
            ->pluck('storage_location')
            ->values()
            ->all();

        return Inertia::render('Pantry/Index', [
            'items' => $items->values()->all(),
            'stats' => $stats,
            'storageLocations' => $storageLocations,
            'units' => Unit::all()->values()->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_code' => 'required|exists:units,code',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'storage_location' => 'nullable|string|max:255',
            'opened' => 'boolean',
        ]);

        $pantryItem = $request->user()->pantryItems()->create($validated);

        return redirect()->back()->with('success', 'Article ajouté au garde-manger.');
    }

    public function update(Request $request, PantryItem $pantryItem)
    {
        $this->authorize('update', $pantryItem);

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'unit_code' => 'required|exists:units,code',
            'expiration_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'opened' => 'boolean',
        ]);

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
