<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Route::group(['prefix' => 'auth'], function () {
  *  Route::post('/login','AuthController@authenticate');
  *  Route::post('/register','AuthController@register');
* });
 */
Route::fallback(function(){
  return response()->json(['message' => 'Not Found!'], 404);
});
Route::post('/login','AuthController@authenticate');
Route::post('/register','AuthController@register');
Route::get('/games', 'GameController@getAll');

Route::group(['middleware' => ['auth:api']], function () {
    //User
    Route::get('/user', 'UserController@get');
    Route::put('/user', 'UserController@update');
    Route::post('/image', 'UploadController@upload');
    Route::delete('/logout','AuthController@logout');
    Route::get('/user/{id}/games', 'GameController@getByUser');
    //Game
    Route::post('/game', 'GameController@create');
    Route::get('/game/{id}', 'GameController@get');
    Route::put('/game/{id}', 'GameController@update');
    Route::delete('/game/{id}', 'GameController@delete');    
    //Gallery
    Route::post('game/gallery', 'UploadController@uploadGallery');
    Route::delete('game/gallery/{id}', 'UploadController@deleteImageGallery');
});
