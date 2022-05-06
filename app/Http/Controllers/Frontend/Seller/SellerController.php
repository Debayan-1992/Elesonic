<?php

namespace App\Http\Controllers\Frontend\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('frontendguest')->except('logout');
    }
}
