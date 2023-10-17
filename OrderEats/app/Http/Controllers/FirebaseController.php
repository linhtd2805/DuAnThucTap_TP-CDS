<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseController extends Controller
{
    public function sendNotification()
    {   
        $token = 'AAAAv8d_Q0g:APA91bHwAswiq8GxEgvRqHK5N989MT12PcVHrlfnJDzib6zePmgC_ZillOxtg_v4lcvf2DrL9byIrmZT6lhu7pKK6ts2IyTP_fPoQatOmg1o80rGEx3vzLR1Y_cgYLfSCsjjQ7tFx7ZU';
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

