<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        $reports = $this->reportService->getAllReports(20);
        $stats = $this->reportService->getReportStats();

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'stats' => $stats,
        ]);
    }

    public function store(StoreReportRequest $request)
    {
        try {
            $this->reportService->createReport(
                Auth::id(),
                $request->validated('reportable_type'),
                $request->validated('reportable_id'),
                $request->validated('reason'),
                $request->validated('description')
            );

            return back()->with('success', 'Signalement envoyé');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateReportRequest $request, Report $report)
    {
        $this->reportService->updateReport($report, [
            'status' => $request->validated('status'),
            'resolution_note' => $request->validated('resolution_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Signalement mis à jour');
    }
}
