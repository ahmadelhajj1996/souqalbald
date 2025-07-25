<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\FirebaseService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function test()
    {
        $user = auth()->user();
        $firebaseToken = $user ? $user->firebase_token : null;
        if ($firebaseToken && strlen($firebaseToken) > 0) {
            $title = 'test title';
            $body = 'testing Work';
            $type = 'notification';
            $sub_type = 'test';
            $data = [
                'type' => $type,
                'sub_type' => $sub_type,
            ];

            try {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $sub_type,
                    'data' => json_encode($data),
                    'title' => $title,
                    'body' => $body,
                    'sent_at' => now(),
                    'read' => false,
                ]);
                $this->firebaseService->sendNotification(
                    $data,
                    $title,
                    $body,
                    $firebaseToken
                );
                error_log('Notification sent successfully.');
            } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                error_log('Firebase token not found or invalid: '.$firebaseToken);
            } catch (\Exception $e) {
                error_log('Failed to send notification: '.$e->getMessage());
            }
        }
    }

    public function storeFcmToken(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'firebase_token' => 'required|string|max:16384',
        ]);

        $user->update(['firebase_token' => $request->firebase_token]);

        return $this->successResponse('', 'notification', 'fcm_stored_successfully');
    }

    public function markOneAsRead($id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['read' => true]);

        return $this->successResponse('', 'notification', 'notification_marked_as_read');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return $this->successResponse('', 'notification', 'all_notification_marked_as_read');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->latest('created_at');

        return $this->successResponse($notifications, 'notification', 'notificationـretrievedـsuccessfully');
    }
}
