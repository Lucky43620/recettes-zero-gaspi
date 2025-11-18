<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ReportService
{
    /**
     * Get all reports with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllReports(int $perPage = 20): LengthAwarePaginator
    {
        return Report::with(['reporter', 'reportable', 'reviewer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get reports by status
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getReportsByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return Report::with(['reporter', 'reportable', 'reviewer'])
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get pending reports
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingReports(int $perPage = 20): LengthAwarePaginator
    {
        return Report::with(['reporter', 'reportable', 'reviewer'])
            ->pending()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get reports by reportable type
     *
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getReportsByType(string $type, int $perPage = 20): LengthAwarePaginator
    {
        return Report::with(['reporter', 'reportable', 'reviewer'])
            ->where('reportable_type', $type)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new report
     *
     * @param int $reporterId
     * @param string $reportableType
     * @param int $reportableId
     * @param string $reason
     * @param string|null $description
     * @return Report|null
     */
    public function createReport(
        int $reporterId,
        string $reportableType,
        int $reportableId,
        string $reason,
        ?string $description = null
    ): ?Report {
        try {
            // Check if user has already reported this item
            $existingReport = Report::where('reporter_id', $reporterId)
                ->where('reportable_type', $reportableType)
                ->where('reportable_id', $reportableId)
                ->where('status', '!=', 'dismissed')
                ->first();

            if ($existingReport) {
                Log::warning('Duplicate report attempt', [
                    'reporter_id' => $reporterId,
                    'reportable_type' => $reportableType,
                    'reportable_id' => $reportableId,
                ]);
                return null;
            }

            $report = Report::create([
                'reporter_id' => $reporterId,
                'reportable_type' => $reportableType,
                'reportable_id' => $reportableId,
                'reason' => $reason,
                'description' => $description,
                'status' => 'pending',
            ]);

            Log::info('Report created', [
                'report_id' => $report->id,
                'reporter_id' => $reporterId,
                'reportable_type' => $reportableType,
                'reportable_id' => $reportableId,
                'reason' => $reason,
            ]);

            return $report;
        } catch (\Exception $e) {
            Log::error('Failed to create report', [
                'reporter_id' => $reporterId,
                'reportable_type' => $reportableType,
                'reportable_id' => $reportableId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update report status
     *
     * @param Report $report
     * @param string $status
     * @param int $reviewerId
     * @param string|null $resolutionNote
     * @return bool
     */
    public function updateReportStatus(
        Report $report,
        string $status,
        int $reviewerId,
        ?string $resolutionNote = null
    ): bool {
        try {
            $report->update([
                'status' => $status,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'resolution_note' => $resolutionNote,
            ]);

            Log::info('Report status updated', [
                'report_id' => $report->id,
                'status' => $status,
                'reviewed_by' => $reviewerId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update report status', [
                'report_id' => $report->id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark report as reviewing
     *
     * @param Report $report
     * @param int $reviewerId
     * @return bool
     */
    public function markAsReviewing(Report $report, int $reviewerId): bool
    {
        return $this->updateReportStatus($report, 'reviewing', $reviewerId);
    }

    /**
     * Resolve a report
     *
     * @param Report $report
     * @param int $reviewerId
     * @param string|null $resolutionNote
     * @return bool
     */
    public function resolveReport(Report $report, int $reviewerId, ?string $resolutionNote = null): bool
    {
        return $this->updateReportStatus($report, 'resolved', $reviewerId, $resolutionNote);
    }

    /**
     * Dismiss a report
     *
     * @param Report $report
     * @param int $reviewerId
     * @param string|null $resolutionNote
     * @return bool
     */
    public function dismissReport(Report $report, int $reviewerId, ?string $resolutionNote = null): bool
    {
        return $this->updateReportStatus($report, 'dismissed', $reviewerId, $resolutionNote);
    }

    /**
     * Delete a report
     *
     * @param Report $report
     * @return bool
     */
    public function deleteReport(Report $report): bool
    {
        try {
            $reportId = $report->id;
            $report->delete();

            Log::info('Report deleted', [
                'report_id' => $reportId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get reports for a specific reportable item
     *
     * @param string $reportableType
     * @param int $reportableId
     * @return Collection
     */
    public function getReportsForItem(string $reportableType, int $reportableId): Collection
    {
        return Report::with(['reporter', 'reviewer'])
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->latest()
            ->get();
    }

    /**
     * Get reports by a specific user
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getReportsByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Report::with(['reportable', 'reviewer'])
            ->where('reporter_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get report statistics
     *
     * @return array
     */
    public function getReportStats(): array
    {
        return [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'reviewing' => Report::reviewing()->count(),
            'resolved' => Report::resolved()->count(),
            'dismissed' => Report::dismissed()->count(),
            'by_reason' => Report::selectRaw('reason, COUNT(*) as count')
                ->groupBy('reason')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->reason => $item->count];
                })
                ->toArray(),
            'by_type' => Report::selectRaw('reportable_type, COUNT(*) as count')
                ->groupBy('reportable_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->reportable_type => $item->count];
                })
                ->toArray(),
        ];
    }

    /**
     * Check if an item has been reported by a user
     *
     * @param int $userId
     * @param string $reportableType
     * @param int $reportableId
     * @return bool
     */
    public function hasUserReported(int $userId, string $reportableType, int $reportableId): bool
    {
        return Report::where('reporter_id', $userId)
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->exists();
    }

    /**
     * Get recent reports
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentReports(int $limit = 10): Collection
    {
        return Report::with(['reporter', 'reportable', 'reviewer'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Bulk update reports status
     *
     * @param array $reportIds
     * @param string $status
     * @param int $reviewerId
     * @param string|null $resolutionNote
     * @return int Number of updated reports
     */
    public function bulkUpdateStatus(
        array $reportIds,
        string $status,
        int $reviewerId,
        ?string $resolutionNote = null
    ): int {
        try {
            $count = Report::whereIn('id', $reportIds)
                ->update([
                    'status' => $status,
                    'reviewed_by' => $reviewerId,
                    'reviewed_at' => now(),
                    'resolution_note' => $resolutionNote,
                ]);

            Log::info('Bulk report status update', [
                'count' => $count,
                'status' => $status,
                'reviewed_by' => $reviewerId,
            ]);

            return $count;
        } catch (\Exception $e) {
            Log::error('Failed to bulk update reports', [
                'report_ids' => $reportIds,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }
}
