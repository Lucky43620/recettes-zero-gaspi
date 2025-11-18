<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCooksnapRequest;
use App\Models\Cooksnap;
use App\Models\Recipe;
use App\Services\SettingsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CooksnapController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SettingsService $settings
    ) {}

    public function store(StoreCooksnapRequest $request, Recipe $recipe)
    {
        if (!$this->settings->get('enable_cooksnaps', true)) {
            return back()->with('error', 'Les cooksnaps sont actuellement désactivés par l\'administrateur.');
        }

        $cooksnap = $recipe->cooksnaps()->create([
            'user_id' => Auth::id(),
            'comment' => $request->validated('comment'),
        ]);

        foreach ($request->validated('photos') as $photo) {
            $cooksnap->addMedia($photo)->toMediaCollection('photos');
        }

        return back()->with('success', 'Cooksnap publié');
    }

    public function destroy(Cooksnap $cooksnap)
    {
        $this->authorize('delete', $cooksnap);

        $cooksnap->delete();

        return back()->with('success', 'Cooksnap supprimé');
    }
}
