<?php

use App\Http\Requests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

Route::get('password/reset-form/{token}', 'Api\Auth\ResetPasswordController@resetPasswordForm');
Route::post('password/reset', 'Api\Auth\ResetPasswordController@resetPassword');

Route::post("login", "AuthenticateController@authenticate")->name("login");
Route::post("godfathers/sign-up", "GodfatherController@store");
Route::post('godfathers/{user}/upload-profile-image', 'GodfatherController@uploadProfileImage');

Route::get('events', 'EventController@index');

Route::get('files/{file}/download', 'FileController@download');

//Route::middleware(["jwt.auth"])->group(function () {

    Route::put('users/fcm', 'UserController@updateFCMToken');

    Route::get('godfathers', 'GodfatherController@index');
    Route::get('godfathers/{user}', 'GodfatherController@show');
    Route::get('godfathers/{user}/godsons', 'GodfatherController@getGodsons');
    Route::post('godfathers', 'GodfatherController@store');
    Route::post('godfathers/{user}/godson/{godson}', 'GodfatherController@toggleGodson');
    Route::put('godfathers/{user}', 'GodfatherController@update');
    Route::delete('godfathers/{user}', 'GodfatherController@destroy');

    Route::get('godsons/{godson}/godfathers', 'GodsonController@getGodfathers');
    Route::resource('godsons', 'GodsonController');

    Route::get('threads/{user}', 'ThreadController@userThreads');
    Route::get('threads/{thread}/messages/{start_id}', 'ThreadController@show');
    Route::get('threads/{thread}/files', 'ThreadController@threadFiles');
    Route::post('threads/messages', 'ThreadController@store');
    Route::post('threads/{thread}/file/upload', 'ThreadController@uploadFile');
    Route::put('threads/{thread}/update', 'ThreadController@update');
    Route::delete('threads/{thread}', 'ThreadController@destroy');
    Route::delete('threads/{user}/delete-all', 'ThreadController@destroyAll');

    Route::post('message/{thread}/user/{user}', 'MessageController@store');
    Route::delete('message/{message}', 'MessageController@destroy');

    Route::post('events', 'EventController@store');
    Route::put('events/{event}', 'EventController@update');
    Route::delete('events/{event}', 'EventController@destroy');

    Route::put('notifications', 'NotificationController@destroy');
    //Route::post('notifications/{user_id}/{thread_id}/{message_id}/store', 'NotificacionController@store');
    //Route::put('notifications/{user_id}/{thread_id}/destroy', 'NotificationController@destroy');

//});
