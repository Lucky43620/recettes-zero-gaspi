<?php

namespace App\Http\Controllers;

use App\Services\GdprService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GdprController extends Controller
{
    public function __construct(
        private GdprService $gdprService
    ) {}

    public function exportData()
    {
        $user = Auth::user();
        $path = $this->gdprService->exportUserData($user);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();

        Auth::logout();

        $this->gdprService->deleteUserData($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Votre compte a été supprimé');
    }
}
