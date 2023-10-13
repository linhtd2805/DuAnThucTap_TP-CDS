<?php
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
require __DIR__.'/vendor/autoload.php'; // Đảm bảo đã require autoload.php
$app = require __DIR__.'/bootstrap/app.php'; // Đường dẫn tới tệp app.php của Lumen

// Khởi tạo ứng dụng
$app->withFacades();
$app->withEloquent();

// Bây giờ bạn có thể sử dụng app() để truy cập các dịch vụ của ứng dụng



$firebase = app('firebase.connection');

$messaging = $firebase->createMessaging();

$message = CloudMessage::new()
    ->withNotification(['title' => 'Hello', 'body' => 'This is a test message'])
    ->withData(['key' => 'value']);