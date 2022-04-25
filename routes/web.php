<?php

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

Route::get('/', function () {return view('welcome');})->name('index');

Auth::routes(['verify' => true]);

Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::any('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/register', 'Auth\RegisterController@register')->name('register');

Route::prefix('/dashboard')->name('dashboard.')->namespace('Dashboard')->middleware('auth','checkuser')->group(function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/profile/{id?}', 'HomeController@profile')->name('profile');
    Route::post('/profile', 'HomeController@updateProfile')->name('profile');

    Route::any('/common/fetchdata/{type?}/{fetch?}/{id?}', 'CommonController@fetchdata')->name('fetchdata');

    //== Tools Routes == //
    Route::prefix('/tools')->name('tools.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/roles', 'ToolsController@roles')->name('roles');
        Route::post('/roles/submit', 'ToolsController@submitrole')->name('submitrole');
        Route::get('/permissions', 'ToolsController@permissions')->name('permissions');
        Route::post('/permissions/submit', 'ToolsController@submitpermission')->name('submitpermission');
        Route::get('/role/permissions/{role_id?}', 'ToolsController@rolepermissions')->name('rolepermissions');
        Route::post('/role/permissions/submit', 'ToolsController@rolepermissionssubmit')->name('rolepermissions.submit');
    });

    //== Settings Routes == //
    Route::prefix('/settings')->name('settings.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', 'SettingsController@index')->name('index');
        Route::post('/submit', 'SettingsController@submit')->name('submit');
    });

    //== CMS Routes == //
    Route::prefix('/cms')->name('cms.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/{type}', 'CmsController@index')->name('index');
        Route::get('/{type}/edit/{id?}', 'CmsController@edit')->name('edit');
        Route::post('/content/submit', 'CmsController@submitcms')->name('submitcms');
    });

    //== Blog Routes == //
    Route::prefix('/blogs')->name('blogs.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index', 'BlogsController@index')->name('index');
        Route::get('/add/new', 'BlogsController@add')->name('add');
        Route::get('/edit/{id?}', 'BlogsController@edit')->name('edit');
        Route::post('/submit', 'BlogsController@submit')->name('submit');
    });

    //== Members Routes == //
    Route::prefix('/members')->name('members.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', 'MembersController@index')->name('index');
        Route::get('/add/{type}', 'MembersController@add')->name('add');
        Route::post('/create/{type}', 'MembersController@create')->name('create');
        Route::post('/change-action/submit', 'MembersController@changeaction')->name('changeaction');
        Route::get('/permissions/{id?}', 'MembersController@permission')->name('permission');
        Route::post('/permissions/submit', 'MembersController@permissionsubmit')->name('permissionsubmit');
    });

    //== Members Routes == //
    Route::prefix('/notifications')->name('notifications.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', 'NotificationsController@index')->name('index');
        Route::post('/submit', 'NotificationsController@submit')->name('submit');
    });

    //== Resources Routes == //
    Route::prefix('/resources')->name('resources.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/membership-packages/{type}', 'ResourcesController@packages')->name('packages');
        Route::post('/membership-package/submit', 'ResourcesController@packagesubmit')->name('packagesubmit');
        Route::get('/schemes/{type}', 'ResourcesController@schemes')->name('schemes');
        Route::post('/schemes/submit', 'ResourcesController@schemesubmit')->name('schemesubmit');
        Route::any('/commission/get', 'ResourcesController@getcommission')->name('getcommission');
        Route::post('/commission/submit', 'ResourcesController@commissionsubmit')->name('commissionsubmit');
    });

    //== Setup Routes == //
    Route::prefix('/setup')->name('setup.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/{type}', 'SetupController@index')->name('index');
        Route::post('/submit', 'SetupController@submit')->name('submit');
    });
});
