<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseController extends Controller
{
    public function sendNotification()
    {   
        $token = 'cE3_2LWwQA2KbCM7N9HhvX:APA91bF-yAHHu7BibgXZMeosQVrvEg5d-bx4OWNyW5qBHhnRKXLfDfELqkgEAkdBcjcySiW30mVjXmC9ySdzvK1AZRVJ4zKSZNuHW87v16B3LINQf60l5Wmfmjd5_7lP4mftUBJqqnyP';
        $notificationData = [
            'title' => 'Test Notification',
            'body' => 'This is a test notification from Lumen.',
        ];

        $notification = Notification::fromArray($notificationData);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withTarget('token', $token);

        app('firebase.messaging')->send($message);

        return 'Notification sent!';
    }

}

