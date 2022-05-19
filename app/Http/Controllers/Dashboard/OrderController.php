<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\RequestServiceSubmitMail;
use App\Model\Order;
use App\Model\Order_details;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Model\State;
use App\Model\City;
use App\Model\Product;
use App\Mail\OnlyTextMail;
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

    function view(Request $request,$orderId){
        $order_details = Order_details::where('order_id',$orderId)->leftjoin('products','products.id','=','order_details.order_product_id')->get();
        $order =   Order::where('order_id',$orderId)->first();
        $billing = User::where('id',$order->order_customer_id)->first();
        $shipping = json_decode($order->orderaddress);
        $mystate = State::where('id',$billing->state_id)->first();
        $mycity  = City::where('id',$billing->city_id)->first();
        $data['path'] = asset('public/uploads/order/Order-'.$order->order_unique_id.'.pdf');
        $data['mystate']           = $mystate;
        $data['mycity']            = $mycity;
        $data['billingAddress']    = $billing;
        $data['shippingAddress']   = $shipping;
        $data['order_details']     = $order_details;
        $data['order']             = $order;
        return view('dashboard.order.order_details', $data);
    }

    function statusChange(Request $request){
        $val = $request->val;
        $order_id = $request->order_id;
        $order_details_id = $request->order_details_id;
        $userOrdr =  Order::where('order_id',$order_id)->first();
        $email = User::where('id',$userOrdr->order_customer_id)->first();
        $order_details = Order_details::where('order_details_id',$order_details_id)->first();
        $product_details = Product::where('id',$order_details->order_product_id)->first();
        Order_details::where('order_details_id', $order_details_id)->update([
            'order_product_status' =>  $val,
            'updated_at' => date('Y-m-d h:i:s'),
        ]);
        $orderCount       =   Order_details::where('order_id',$order_id)->count();
        $orderStatusCount =   Order_details::where('order_id',$order_id)->where('order_product_status',$val)->count();
        if($orderCount = $orderStatusCount){
            Order::where('order_id', $order_id)->update([
                'order_status' =>  $val,
                'updated_at' => date('Y-m-d h:i:s'),
            ]);
        }
        if($val == 'Cancelled'){
            $existingQTY = $order_details->cart_item_pro_qty;
            $newQTY = $existingQTY + $product_details->quantity;
            Product::where('id', $order_details->order_product_id)->update([
                        'quantity' =>  $newQTY,
                    ]);
        }
        $txt = 'Your Order for '.$userOrdr->order_unique_id.' is '.$val;
        $subject = 'Order Status E-mail';
        $mailFromId = config()->get('mail.from.address');
        Mail::to($email->email)->send(new OnlyTextMail("", $mailFromId, $txt, $subject));
        $res = 1;
        echo json_encode($res);
    }

    
}
