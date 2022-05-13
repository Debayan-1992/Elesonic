<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\RequestServiceSubmitMail;
use App\Model\Order;
use App\Model\Order_details;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    //
    public function index()
    {
        $data['activemenu'] = array(
            'main' => 'orders',
            'sub' => 'orders',
        );
        return view('dashboard.order.index', $data);
    }

    
}
