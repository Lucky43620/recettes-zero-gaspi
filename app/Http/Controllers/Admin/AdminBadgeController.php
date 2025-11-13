<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminBadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::withCount('users')->latest()->paginate(20);

        return Inertia::render('Admin/Badges/Index', [
            'badges' => $badges,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:badges,key',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'required_count' => 'required|integer|min:1',
        ]);

        Badge::create($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge créé');
    }

    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:badges,key,' . $badge->id,
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'required_count' => 'required|integer|min:1',
        ]);

        $badge->update($validated);

        return back()->with('success', 'Badge mis à jour');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge supprimé');
    }
}
