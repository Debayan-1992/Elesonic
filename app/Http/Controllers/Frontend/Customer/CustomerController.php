<?php

namespace App\Http\Controllers\Frontend\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('frontendguest')->except('logout');
    }
}
