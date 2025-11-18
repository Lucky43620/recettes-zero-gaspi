<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Get paginated notifications for a user
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserNotifications(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }

    /**
     * Get unread notifications for a user
     *
     * @param User $user
     * @param int|null $limit
     * @return Collection
     */
    public function getUnreadNotifications(User $user, ?int $limit = null): Collection
    {
        $query = $user->unreadNotifications();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get unread notification count for a user
     *
     * @param User $user
     * @return int
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark a specific notification as read
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        try {
            $notification = $user->notifications()->findOrFail($notificationId);
            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param User $user
     * @return bool
     */
    public function markAllAsRead(User $user): bool
    {
        try {
            $user->unreadNotifications->markAsRead();

            Log::info('All notifications marked as read', [
                'user_id' => $user->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete a specific notification
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function deleteNotification(User $user, string $notificationId): bool
    {
        try {
            $notification = $user->notifications()->findOrFail($notificationId);
            $notification->delete();

            Log::info('Notification deleted', [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete notification', [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete all notifications for a user
     *
     * @param User $user
     * @return bool
     */
    public function deleteAllNotifications(User $user): bool
    {
        try {
            $user->notifications()->delete();

            Log::info('All notifications deleted', [
                'user_id' => $user->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete all notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete old read notifications
     *
     * @param User $user
     * @param int $daysOld
     * @return int Number of deleted notifications
     */
    public function deleteOldReadNotifications(User $user, int $daysOld = 30): int
    {
        try {
            $count = $user->readNotifications()
                ->where('read_at', '<', now()->subDays($daysOld))
                ->delete();

            Log::info('Old read notifications deleted', [
                'user_id' => $user->id,
                'days_old' => $daysOld,
                'count' => $count,
            ]);

            return $count;
        } catch (\Exception $e) {
            Log::error('Failed to delete old read notifications', [
                'user_id' => $user->id,
                'days_old' => $daysOld,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get notifications by type
     *
     * @param User $user
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getNotificationsByType(User $user, string $type, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()
            ->where('type', $type)
            ->paginate($perPage);
    }

    /**
     * Send notification to user
     *
     * @param User $user
     * @param mixed $notification
     * @return bool
     */
    public function sendNotification(User $user, $notification): bool
    {
        try {
            $user->notify($notification);

            Log::info('Notification sent', [
                'user_id' => $user->id,
                'notification_type' => get_class($notification),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'notification_type' => get_class($notification),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send notification to multiple users
     *
     * @param Collection $users
     * @param mixed $notification
     * @return array
     */
    public function sendBulkNotification(Collection $users, $notification): array
    {
        $successCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            if ($this->sendNotification($user, $notification)) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        Log::info('Bulk notification sent', [
            'notification_type' => get_class($notification),
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ]);

        return [
            'success' => $successCount,
            'failed' => $failedCount,
            'total' => $users->count(),
        ];
    }

    /**
     * Get notification statistics for a user
     *
     * @param User $user
     * @return array
     */
    public function getNotificationStats(User $user): array
    {
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
            'by_type' => $user->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->type => $item->count];
                })
                ->toArray(),
        ];
    }
}
