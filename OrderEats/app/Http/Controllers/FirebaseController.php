<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Messaging;
use Kreait\Laravel\Firebase;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
        public function sendFcmMessage()
    {
        // Initialize Firebase
        $firebase = app(Firebase::class);

        // Create a messaging instance
        $messaging = $firebase->getMessaging();

        // Create an FCM message
        $message = CloudMessage::new()
            ->withNotification(['title' => 'Hello', 'body' => 'This is a test message'])
            ->withData(['key' => 'value']);

        // Send the FCM message
        $messaging->send($message);

        return response('FCM message sent.');
    }
}
