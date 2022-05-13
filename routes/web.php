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
use App\Http\Controllers\Frontend\Customer\CustomerController;
use App\Http\Controllers\Frontend\Seller\SellerController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Frontend\FrontendNoAuthController;
use App\Http\Controllers\Dashboard\OrderController;

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
//A controller at route should be made for redirecting non middleware checked views, like homepage
Route::get('/welcome', [HomeController::class, 'welcome'])->name('welcome');

Route::get('/', [FrontendNoAuthController::class, 'index'])->name('index'); //When accessing / from admin goest to / route of admin, this should be in a place where their is no verification
Route::get('/product-list/{type?}', [FrontendNoAuthController::class, 'product_list'])->name('product-list');
Route::get('/product-details/{type?}/{id?}', [FrontendNoAuthController::class, 'product_details'])->name('product-details');
Route::post('/get-filter-data', [FrontendNoAuthController::class, 'get_filter_data'])->name('get-filter-data');
Route::get('/services', [FrontendNoAuthController::class, 'services'])->name('services');

Route::get('/faq', [FrontendNoAuthController::class, 'faq'])->name('faq');
Route::get('/content-details/{type?}', [FrontendNoAuthController::class, 'content_details'])->name('content-details');
Route::get('/contact-us', [FrontendNoAuthController::class, 'contact_us'])->name('contact_us');
Route::post('/contact-us', [FrontendNoAuthController::class, 'contact_us_post'])->name('contact_us');

Route::post('/get-search-data', [FrontendNoAuthController::class, 'get_search_data'])->name('get-search-data');
Route::get('/search-product', [FrontendNoAuthController::class, 'search_product'])->name('search-product');

Route::post('/subscribeEmail', [FrontendNoAuthController::class, 'subscribeEmail'])->name('subscribeEmail');

Route::get('/departments', [FrontendNoAuthController::class, 'departments'])->name('departments');

Route::post('/add-cart', [FrontendNoAuthController::class, 'add_cart'])->name('add-cart');

Route::post('/buy-now', [FrontendNoAuthController::class, 'buy_now'])->name('buy-now');

// Frontend Login, Registration, Pass reset Routes...
Route::get('/login', [FrontendController::class, 'signin'])->name('login'); //Frontend Login
Route::post('/login', [FrontendController::class, 'signin_post'])->name('login_post'); 
Route::get('/signup', [FrontendController::class, 'signup'])->name('signup');
Route::post('/signup', [FrontendController::class, 'signup_post'])->name('signup_post');
Route::get('/password-reset', [FrontendController::class, 'show_passwordreset_form'])->name('frontend_password_reset');
Route::post('/password-reset', [FrontendController::class, 'passwordreset_post'])->name('frontend_password_reset');
Route::post('/pass-update', [FrontendController::class, 'resetPassword'])->name('resetPassword');

// Admin Login, Registration, Pass reset Routes...
Route::get('/admin/register', 'Auth\RegisterController@showRegistrationForm')->name('admin_register_form');
Route::post('/admin/register', [Auth\RegisterController::class, 'register'])->name('admin_register');
Route::get('/admin/login', [Auth\LoginController::class, 'showLoginForm'])->name('admin_login_form'); //AuthenticatesUsers trait's showLoginForm function is being overridden by LoginController class's showLoginForm function
Route::post('/admin/login', [Auth\LoginController::class, 'login'])->name('admin_login_form'); //AuthenticatesUsers trait's showLoginForm function is being overridden by LoginController class's showLoginForm function
Route::any('/admin/logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::name('password.')->group(function() {
    Route::get('/admin/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('request');
    Route::post('/admin/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('email');
    Route::get('/admin/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('reset');
    Route::post('/admin/password/reset', 'Auth\ResetPasswordController@reset')->name('reset');
});

//Common to Admin(admin,superadmin) and Frontend(customer,seller), these should be changed to non middleware checked controller
Route::get('/validate-user', [HomeController::class, 'validate_user']); //Email verify
Route::get('/password-reset-form', [FrontendController::class, 'pass_reset_form_show']); //Frontned Password reset form
Route::get('/logout', [HomeController::class, 'logout'])->name('lgt'); 
//Frontend Password reset form

//Admin routes
Route::prefix('/admin/dashboard')->name('dashboard.')->namespace('Dashboard')->middleware('adminauth','checkuser')->group(function(){
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

    Route::prefix('/orders')->name('orders.')->middleware('checkrole:superadmin|admin')->group(function(){
        Route::get('/index/{type}', [OrderController::class, 'index'])->name('index');
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

    Route::prefix('/request-service')->name('request_service.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', [ServiceController::class, 'r_service_index'])->name('index');
        Route::post('/delete', [ServiceController::class, 'delete'])->name('delete');
        //Route::post('/statusChange', [ServiceController::class, 'r_service_statusChange'])->name('statusChange');
        Route::post('/submit', [ServiceController::class, 'r_service_submit'])->name('r_service_submit');
    });

    //== Departments ==//
    Route::prefix('/department')->name('department.')->middleware('checkrole:superadmin')->group(function(){
        Route::get('/index', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('submit');
        Route::post('/delete', [DepartmentController::class, 'delete'])->name('delete');
        Route::post('/statusChange', [DepartmentController::class, 'statusChange'])->name('statusChange');
    });
});

//Frontnend Customer routes
Route::prefix('/customer/dashboard')->name('customer.')->middleware('customerauth','checkuser')->group(function(){
    Route::get('/', [FrontendNoAuthController::class, 'dashboard'])->name('customer_dashboard');
    Route::get('password-change', [FrontendNoAuthController::class, 'password_change_form'])->name('frontend_change_pass');
    Route::post('password-change', [FrontendNoAuthController::class, 'password_change_update'])->name('frontend_pass_upd');
    Route::post('account-update', [FrontendNoAuthController::class, 'my_account_update'])->name('frontend_acc_upd');

    Route::get('carts', [FrontendNoAuthController::class, 'carts'])->name('carts');
    Route::post('update-product-cart', [FrontendNoAuthController::class, 'update_product_cart'])->name('update-product-cart');
    Route::post('del-product-cart', [FrontendNoAuthController::class, 'del_product_cart'])->name('del-product-cart');
    Route::get('address', [FrontendNoAuthController::class, 'address'])->name('address');
    Route::post('getcity', [FrontendNoAuthController::class, 'get_city'])->name('getcity');
    Route::post('makeDefault', [FrontendNoAuthController::class, 'makeDefault'])->name('makeDefault');
    Route::post('makeDelete', [FrontendNoAuthController::class, 'makeDelete'])->name('makeDelete');
    Route::post('addaddress', [FrontendNoAuthController::class, 'addaddress'])->name('addaddress');
    Route::get('confirm-order', [FrontendNoAuthController::class, 'confirm_order'])->name('confirm-order');
    Route::post('addaddressdef', [FrontendNoAuthController::class, 'addaddressdef'])->name('addaddressdef');
    Route::get('place-order', [FrontendNoAuthController::class, 'place_order'])->name('place-order');
    Route::get('order-now', [FrontendNoAuthController::class, 'order_now'])->name('order-now');
    Route::get('my-order', [FrontendNoAuthController::class, 'my_order'])->name('my-order');

    Route::post('/servicebook', [FrontendNoAuthController::class, 'servicebook'])->name('servicebook');

    Route::get('my-services', [FrontendNoAuthController::class, 'my_services'])->name('my-services');

    Route::get('/order-details/{id?}', [FrontendNoAuthController::class, 'order_details'])->name('order-details');
    
});

//Frontend Seller routes
Route::prefix('/seller/dashboard')->name('seller.')->middleware('sellerauth','checkuser')->group(function(){
    Route::get('/', [FrontendNoAuthController::class, 'dashboard'])->name('seller_dashboard');
    Route::get('password-change', [FrontendNoAuthController::class, 'password_change_form'])->name('frontend_change_pass');
    Route::post('password-change', [FrontendNoAuthController::class, 'password_change_update'])->name('frontend_pass_upd');
    Route::post('account-update', [FrontendNoAuthController::class, 'my_account_update'])->name('frontend_acc_upd');
});
    //Route::get('/login', [FrontendController::class, 'signin'])->name('login'); //Frontend Login
    //Auth::routes(['verify' => true]); //This has been removed because these routes are being manually used
    
    //Auth routes
    //
    //Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin_login'); //Admin panel login
    //Route::post('login', 'Auth\LoginController@login')->name('login'); //Original login controller which comes from Laravel Auth