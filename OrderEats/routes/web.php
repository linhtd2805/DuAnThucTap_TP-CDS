<?php
use Illuminate\Support\Facades\Route;
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
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::get('/logout', 'Auth\\LoginController@logout');   
});