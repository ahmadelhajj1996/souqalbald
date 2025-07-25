<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    /**
     * Create a new class instance.
     */
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials.file'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification(array $data, string $title, string $body, string $token)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $this->messaging->send($message);
    }
}
