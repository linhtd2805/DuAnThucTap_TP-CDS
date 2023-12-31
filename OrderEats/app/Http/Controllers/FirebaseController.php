<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class FirebaseController extends Controller
{
    public function updateDeviceToken(Request $request)
    {
        Auth::user()->device_token =  $request->token;

        Auth::user()->save();

        return response()->json(['Lấy Token FCM thành công.']);
    }

    public function sendNotification($id, $title, $body) {
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Lấy device_token của người dùng dựa trên user_id
        $user = User::find($id);
        if (!$user->device_token) {
            return response()->json("Tài khoản này chưa có Device Token");
        }
        $deviceTokens = [$user->device_token];

        $serverKey = 'AAAAv8d_Q0g:APA91bHwAswiq8GxEgvRqHK5N989MT12PcVHrlfnJDzib6zePmgC_ZillOxtg_v4lcvf2DrL9byIrmZT6lhu7pKK6ts2IyTP_fPoQatOmg1o80rGEx3vzLR1Y_cgYLfSCsjjQ7tFx7ZU'; // ADD SERVER KEY HERE PROVIDED BY FCM

        $data = [
            "registration_ids" => $deviceTokens,
            "notification" => [
                "title" => $title, // Truyền title
                "body" => $body,  // Truyền body thông báo
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Tạm thời vô hiệu hóa hỗ trợ Chứng chỉ SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Thực hiện POST
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Đóng kết nối
        curl_close($ch);
        // FCM response
        // dd($result);
}

public function sendNotification1(Request $request, $id)
{
    $title = $request->input('title');
    $body = $request->input('body');

    $url = 'https://fcm.googleapis.com/fcm/send';

    // Lấy device_token của người dùng dựa trên user_id
    $user = User::find($id);

    if (!$user->device_token) {
        return response()->json("Tài khoản này chưa có Device Token");
    }
    
    $deviceTokens = [$user->device_token];

    $serverKey = 'AAAAv8d_Q0g:APA91bHwAswiq8GxEgvRqHK5N989MT12PcVHrlfnJDzib6zePmgC_ZillOxtg_v4lcvf2DrL9byIrmZT6lhu7pKK6ts2IyTP_fPoQatOmg1o80rGEx3vzLR1Y_cgYLfSCsjjQ7tFx7ZU'; // ADD SERVER KEY HERE PROVIDED BY FCM

    $data = [
        "registration_ids" => $deviceTokens,
        "notification" => [
            "title" => $title,
            "body" => $body,
        ]
    ];

    $encodedData = json_encode($data);

    $headers = [
        'Authorization:key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    // Tạm thời vô hiệu hóa hỗ trợ Chứng chỉ SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

    // Thực hiện POST
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Đóng kết nối
    curl_close($ch);

    // FCM response
    dd($result);
}


    
}

