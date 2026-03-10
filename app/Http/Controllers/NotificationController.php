<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return $this->feed($request);
        }

        return view('personal.notifications');
    }

    public function feed(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $filter = strtolower(trim((string) $request->query('filter', 'all')));
        $perPage = (int) $request->query('per_page', 25);
        $perPage = max(5, min(100, $perPage));

        $query = match ($filter) {
            'unread' => $user->unreadNotifications(),
            default => $user->notifications(),
        };

        $paginator = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DatabaseNotification $notification) => $this->serialize($notification)),
            'meta' => [
                'unread_count' => $user->unreadNotifications()->count(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function markRead(DatabaseNotification $notification): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (
            (int) $notification->notifiable_id !== (int) $user->id
            || (string) $notification->notifiable_type !== User::class
        ) {
            abort(404);
        }

        $notification->markAsRead();

        return response()->json([
            'data' => $this->serialize($notification->fresh()),
            'meta' => [
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    private function serialize(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $targetRoute = $data['target_route'] ?? null;

        $actionUrl = null;
        if (is_string($targetRoute) && $targetRoute !== '') {
            try {
                $actionUrl = route($targetRoute);
            } catch (\Throwable) {
                $actionUrl = null;
            }
        }

        return [
            'id' => (string) $notification->id,
            'title' => (string) ($data['title'] ?? 'Notification'),
            'message' => (string) ($data['message'] ?? ''),
            'type' => (string) ($data['type'] ?? 'info'),
            'created_at' => $notification->created_at?->toIso8601String(),
            'read_at' => $notification->read_at?->toIso8601String(),
            'is_read' => $notification->read_at !== null,
            'action_url' => $actionUrl,
            'target_route' => $targetRoute,
        ];
    }
}

