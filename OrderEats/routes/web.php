<?php
use Illuminate\Support\Facades\Route;
use App\HTTP\Controllers\FirebaseController;
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
Route::get('/reviews' , 'ReviewController@index');
Route::get('/reviews/{id}' , 'ReviewController@show');
Route::post('/reviews', 'ReviewController@store');
Route::put('/reviews/{id}', 'ReviewController@update');
Route::delete('/reviews/{id}', 'ReviewController@destroy');

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/protected', function () {
        return response()->json(['message' => 'Access to protected resources granted!']);
    });

});

$router->group(['prefix' => 'api'], function () use ($router) {
    Route::post('/login', 'Auth\\LoginController@login'); 
    Route::post('/register', 'Auth\\RegisterController@register');
    Route::post('/logout', 'Auth\\LoginController@logout');   
    Route::get('/me', 'Auth\\LoginController@userDetails');  
    Route::get('/check-login', 'Auth\\LoginController@checkLogin'); 
    Route::post('/update-profile', 'Auth\\LoginController@updateProfile');   
});


$router->get('/menus', 'MenusController@index');
$router->get('/menus/{id}', 'MenusController@show');
$router->post('/menus/create', 'MenusController@store');
$router->post('/menus/update/{id}', 'MenusController@update');
$router->delete('/menus/delete/{id}', 'MenusController@destroy');

Route::group(['middleware' => 'auth'],function () use ($router) {
    Route::post('/store-token', 'FirebaseController@updateDeviceToken');
    Route::post('/send-web-notification', 'FirebaseController@sendNotification');
});

