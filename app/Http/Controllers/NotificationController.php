<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get dynamic notifications for logged-in user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $notifications = $user->notifications()->take(15)->get()->map(function ($n) {
            $data = $n->data ?? [];
            
            $jobId = $data['job_id'] ?? null;
            $url = $data['url'] ?? ($jobId ? route('opportunities.show', ['id' => $jobId]) : route('dashboard'));

            return [
                'id' => $n->id,
                'title' => $data['title'] ?? "Job Vacancy Alert: {$data['job_title']}",
                'message' => $data['message'] ?? "A new job opportunity matching your skills has been published.",
                'type' => $data['type'] ?? 'job',
                'url' => $url,
                'read_at' => $n->read_at?->toISOString(),
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            if ($id === 'all') {
                $user->unreadNotifications->markAsRead();
            } else {
                $user->unreadNotifications()->where('id', $id)->first()?->markAsRead();
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }
}
