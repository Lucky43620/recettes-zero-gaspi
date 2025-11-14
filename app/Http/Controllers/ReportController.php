<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'reportable'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function store(StoreReportRequest $request)
    {
        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_type' => $request->validated('reportable_type'),
            'reportable_id' => $request->validated('reportable_id'),
            'reason' => $request->validated('reason'),
            'description' => $request->validated('description'),
        ]);

        return back()->with('success', 'Signalement envoyé');
    }

    public function update(UpdateReportRequest $request, Report $report)
    {
        $report->update([
            'status' => $request->validated('status'),
            'resolution_note' => $request->validated('resolution_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Signalement mis à jour');
    }
}
