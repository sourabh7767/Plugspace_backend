<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/clearcache', function () {
	Artisan::call('optimize:clear');
});
Route::get('/', function () {
    return view('welcome');
});
Route::get('mail', 'RegisterController@getVerifyMail');
Route::get('resetPwd', 'RegisterController@resetPwdMail');
Route::get('resetPwdMail1', 'RegisterController@resetPwdMail1');
Route::get('resetPwdDone', 'RegisterController@resetPwdDone');

Auth::routes();

Route::get('admin/home', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.home');

Route::post('admin/logout', 'RegisterController@adminLogout')->name('admin.logout');

Route::resource('/user', 'RegisterController');
Route::get('admin/user', 'Admin\UserController@index')->name('admin.user');
//Route::get('admin/user/test', 'Admin\UserController@index_test')->name('admin.user.test');
Route::get('admin/users/{type}', 'Admin\UserController@allUser');




Route::get('admin/notification', 'Admin\UserController@notification')->name('admin.notification');

Route::middleware(['middleware' => 'auth'])->group(function () {

    Route::post('admin/plugspaceRank','Admin\UserController@plugspaceRank');
    Route::post('admin/users/plugspaceRank','Admin\UserController@plugspaceRank');
    
    Route::get('admin/plugspaceUser','Admin\UserController@plugspaceUser');
    Route::post('admin/addUser','Admin\UserController@addUser');
    Route::post('admin/editUser','Admin\UserController@editUser');
    Route::post('admin/updateUser','Admin\UserController@updateUser');
    Route::post('admin/deleteUser','Admin\UserController@deleteUser');
    Route::post('admin/deleteUsers','Admin\UserController@deleteUsers');
    Route::post('admin/users/deleteUsers','Admin\UserController@deleteUsers');
    
    
    Route::get('admin/plugspaceText','Admin\RankingController@plugspaceText');
    Route::post('admin/addText','Admin\RankingController@addText');
    Route::post('admin/editText','Admin\RankingController@editText');
    Route::post('admin/updateText','Admin\RankingController@updateText');
    Route::post('admin/deleteText','Admin\RankingController@deleteText');

	Route::get('admin/listMessage','Admin\MessagesController@listMessage');
	Route::post('admin/addMessage','Admin\MessagesController@addMessage');
	Route::post('admin/editMessage','Admin\MessagesController@editMessage');
	Route::post('admin/updateMessage','Admin\MessagesController@updateMessage');
	Route::post('admin/deleteMessage','Admin\MessagesController@deleteMessage');
	    
    
    Route::get('admin/userDetails/{user_id}','Admin\UserController@userDetails');
    Route::post('admin/userStatus','Admin\UserController@userStatus');
    Route::post('admin/users/userStatus','Admin\UserController@userStatus');
    Route::post('admin/removeMedia','Admin\UserController@removeMedia');
    Route::post('admin/removeUsers','Admin\UserController@removeUsers');
});

