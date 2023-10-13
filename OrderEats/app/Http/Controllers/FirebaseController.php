<?php

namespace App\Http\Controllers;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Messaging;

use Kreait\Firebase;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    private $firebase;

    public function __construct(Firebase $firebase)
    {
        $this->firebase = $firebase;
    }
    public function getFirebase()
    {
        $messaging = $this->firebase->getMessaging();
    
        $message = CloudMessage::new()
            ->withNotification(['title' => 'Hello', 'body' => 'This is a test message'])
            ->withData(['key' => 'value']);
    
        $messaging->send($message);
    
        return response('FCM message sent.');
    }
}
