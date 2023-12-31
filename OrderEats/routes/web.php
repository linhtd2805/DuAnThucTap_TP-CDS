<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/testDB', 'Controller@checkConnection');
Route::get('/reviews', 'ReviewController@index');
Route::get('/reviews/{id}', 'ReviewController@show');
Route::put('/reviews/{id}', 'ReviewController@update');
Route::delete('/reviews/{id}', 'ReviewController@destroy');
Route::get('/reviews/search/{keyword}', 'ReviewController@search');
Route::put('/reviews/softDelete/{id}', 'ReviewController@softDelete');
Route::put('/reviews/reverse/{id}', 'ReviewController@reverse');

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/protected', function () {
        return response()->json(['message' => 'Access to protected resources granted!']);
    });

});

$router->group(['prefix' => 'api'], function () use ($router) {
    Route::post('/login', 'Auth\\LoginController@login');
    Route::get('/register', 'Auth\\RegisterController@getRegister');
    Route::post('/register', 'Auth\\RegisterController@register');
    Route::post('/registershipper', 'Auth\\RegistershipperController@register');
    Route::post('/logout', 'Auth\\LoginController@logout');
    Route::get('/me', 'Auth\\LoginController@userDetails');
    Route::get('/check-login', 'Auth\\LoginController@checkLogin');
    Route::put('/update-profile', 'Auth\\LoginController@updateProfile');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/menus', 'MenusController@index');
    $router->get('/menus/{id}', 'MenusController@show');
    $router->post('/menus/create', 'MenusController@store');
    $router->post('/menus/update/{id}', 'MenusController@update');
    $router->delete('/menus/delete/{id}', 'MenusController@destroy');
});

Route::group(['middleware' => 'auth'], function () use ($router) {
    Route::post('/store-token', 'FirebaseController@updateDeviceToken');
    Route::post('/reviews', 'ReviewController@store');
});

// gửi thông báo
Route::post('/send-web-notification/{id}', 'FirebaseController@sendNotification1');
Route::post('/send-web-notification1/{id}', 'FirebaseController@sendNotification');
$router->group(['middleware' => 'auth'], function () use ($router) {
    // Route cho admin
    $router->get('/index/order-history', 'ActivityLogController@Index');


});


/*role*/
$router->get('/role', 'RoleController@index');
$router->get('/role/{id}', 'RoleController@show');
$router->post('/role', 'RoleController@create');
$router->put('/role/{id}', 'RoleController@update');
$router->delete('/role/{id}', 'RoleController@destroy');

// user 
Route::group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/user', 'UserController@index');
    $router->get('/user/{id}', 'UserController@show');
    $router->put('/user/{id}', 'UserController@update');
    $router->get('/user/search/{keyword}', 'UserController@search');

    // $router->delete('/user/{id}', 'UserController@destroy');

    //
});
//index
//$router->get('/calculateDistance', 'ApiController@calculateDistance');

//index
//$router->get('/calculateDistance', 'ApiController@calculateDistance');

//Maps
$router->post('/calculate-distance', 'ApiController@calculateDistance');



//order
$router->get('/orders', 'OrdersController@index');
$router->get('/orders/{id}', 'OrdersController@show');
$router->put('/orders/{id}', 'OrdersController@update');
$router->post('/orders', 'OrdersController@create');
$router->delete('/orders/{id}', 'OrdersController@delete');

//shipper
Route::get('/shipper', 'ShipperCheckOrderController@index');
Route::get('/shipper/{id}', 'ShipperCheckOrderController@show');
Route::put('/shipper/{id}', 'ShipperCheckOrderController@update');

Route::put('/confirmed/{id}', 'Auth\\RegistershipperController@confirmShipper');
Route::put('/unconfirmed/{id}', 'Auth\\RegistershipperController@unconfirmShipper');
Route::get('/confirmed', 'Auth\\RegistershipperController@getConfirmedShippers');
Route::get('/unconfirmed', 'Auth\\RegistershipperController@getUnconfirmedShippers');
