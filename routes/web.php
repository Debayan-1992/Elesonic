<?php

use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\AttributeController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\LandingBannerController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\ToolsController;
use App\Http\Controllers\Dashboard\DepartmentController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Dashboard\MembersController;
use App\Http\Controllers\Auth;

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

// Route::get('/i', function () {return view('welcome');})->name('i');
Route::get('/welcome', [HomeController::class, 'welcome'])->name('welcome');
//Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/login', [FrontendController::class, 'signin'])->name('login'); //Frontend Login
Route::post('login', [FrontendController::class, 'signin_post'])->name('login'); 
Route::get('/signup', [FrontendController::class, 'signup'])->name('signup');
Route::post('/signup', [FrontendController::class, 'signup_post'])->name('signup');
Route::get('/contact_us', [FrontendController::class, 'contact_us'])->name('contact_us');

//Auth::routes(['verify' => true]); //This has been removed because these routes are being manually used

//Auth routes
//
//Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin_login'); //Admin panel login
//Route::post('login', 'Auth\LoginController@login')->name('login'); //Original login controller which comes from Laravel Auth


// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register')->name('register');

//
Route::get('/admin/login', [Auth\LoginController::class, 'showLoginForm'])->name('admin_login_form'); //AuthenticatesUsers trait's showLoginForm function is being overridden by LoginController class's showLoginForm function
Route::post('/admin/login', [Auth\LoginController::class, 'login'])->name('admin_login_form'); //AuthenticatesUsers trait's showLoginForm function is being overridden by LoginController class's showLoginForm function
Route::any('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/register', 'Auth\RegisterController@register')->name('register');

Route::get('/validate-user', [HomeController::class, 'validate_user']); //Email verify

// Password Reset Routes...
Route::name('password.')->group(function() {
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('reset');
});

Route::prefix('/admin/dashboard')->name('dashboard.')->namespace('Dashboard')->middleware('auth','checkuser')->group(function(){
    //Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/profile/{id?}', [HomeController::class, 'profile'])->name('profile');
    Route::post('/profile', [HomeController::class, 'updateProfile'])->name('profile');

    Route::any('/common/fetchdata/{type?}/{fetch?}/{id?}', 'CommonController@fetchdata')->name('fetchdata');

    //== Tools Routes == //
    Route::prefix('/tools')->name('tools.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/roles', [ToolsController::class, 'roles'])->name('roles');
        Route::post('/roles/submit', [ToolsController::class, 'submitrole'])->name('submitrole');
        Route::get('/permissions', [ToolsController::class, 'permissions'])->name('permissions');
        Route::post('/permissions/submit', [ToolsController::class, 'submitpermission'])->name('submitpermission');
        Route::get('/role/permissions/{role_id?}', [ToolsController::class, 'rolepermissions'])->name('rolepermissions');
        Route::post('/role/permissions/submit', [ToolsController::class, 'rolepermissionssubmit'])->name('rolepermissions.submit');
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
        Route::get('/index/{type}', [MembersController::class, 'index'])->name('index');
        Route::get('/add/{type}', [MembersController::class, 'add'])->name('add');
        Route::post('/create/{type}', [MembersController::class, 'create'])->name('create');
        Route::post('/change-action/submit', [MembersController::class, 'changeaction'])->name('changeaction');
        Route::get('/permissions/{id?}', [MembersController::class, 'permission'])->name('permission');
        Route::post('/permissions/submit', [MembersController::class, 'permissionsubmit'])->name('permissionsubmit');
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

      //== Category Routes == //
    Route::prefix('/category')->name('category.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', [CategoryController::class, 'index'])->name('index');
        Route::post('/store',       [CategoryController::class, 'store'])->name('store');
        Route::post('/statusChange',[CategoryController::class, 'statusChange'])->name('statusChange');
        Route::get('/edit/{id}',    [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update',      [CategoryController::class, 'update'])->name('update');
    });

    //== Attributes Routes == //
    Route::prefix('/attributes')->name('attributes.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', [AttributeController::class, 'index'])->name('index');
        Route::post('/store',       [AttributeController::class, 'store'])->name('store');
        Route::post('/statusChange',[AttributeController::class, 'statusChange'])->name('statusChange');
        Route::get('/edit/{id}',    [AttributeController::class, 'edit'])->name('edit');
        Route::post('/update',      [AttributeController::class, 'update'])->name('update');
    });

    //== Brand Routes == //
    Route::prefix('/brand')->name('brand.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', [BrandController::class, 'index'])->name('index');
        Route::post('/store',       [BrandController::class, 'store'])->name('store');
        Route::post('/statusChange',[BrandController::class, 'statusChange'])->name('statusChange');
        Route::get('/edit/{id}',    [BrandController::class, 'edit'])->name('edit');
        Route::post('/update',      [BrandController::class, 'update'])->name('update');
    });

    //== Products Routes == //
    Route::prefix('/product')->name('product.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', [ProductController::class, 'index'])->name('index');
        Route::get('/create',       [ProductController::class, 'create'])->name('create');
        Route::post('/store',       [ProductController::class, 'store'])->name('store');
        Route::post('/statusChange',[ProductController::class, 'statusChange'])->name('statusChange');
        Route::get('/edit/{id}',    [ProductController::class, 'edit'])->name('edit');
        Route::post('/update',      [ProductController::class, 'update'])->name('update');
        Route::post('/imageDelete', [ProductController::class, 'imageDelete'])->name('imageDelete');
    });

    //== Landing Banner ==//
    Route::prefix('/banner')->name('banner.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', [LandingBannerController::class, 'index'])->name('index');
        Route::post('/store', [LandingBannerController::class, 'store'])->name('submit');
        Route::post('/delete', [LandingBannerController::class, 'delete'])->name('delete');
        Route::post('/statusChange', [LandingBannerController::class, 'statusChange'])->name('statusChange');
    });

    //== Services ==//
    Route::prefix('/service')->name('service.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', [ServiceController::class, 'index'])->name('index');
        Route::post('/store', [ServiceController::class, 'store'])->name('submit');
        Route::post('/delete', [ServiceController::class, 'delete'])->name('delete');
        Route::post('/statusChange', [ServiceController::class, 'statusChange'])->name('statusChange');
    });

    //== Departments ==//
    Route::prefix('/department')->name('department.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('submit');
        Route::post('/delete', [DepartmentController::class, 'delete'])->name('delete');
        Route::post('/statusChange', [DepartmentController::class, 'statusChange'])->name('statusChange');
    });
});

Route::name('frontend.')->middleware('frontendauth','checkuser')->group(function(){
    Route::get('/i', [FrontendController::class, 'd_index'])->name('d_index');
});
