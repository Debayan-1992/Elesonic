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
use App\Exports\OrderReportsExportByRange;
use Excel;
class ReportController extends Controller
{
    //
    public function index()
    {
        $data['activemenu'] = array(
            'main' => 'reports',
            'sub' => 'reports',
        );
        return view('dashboard.report.index', $data);
    }

    function generate_excel_revenue(Request $request){
        $startdate = $request->startDate;
        $enddate = $request->endDate;
        $orders = Order_details::whereBetween('order_details.created_at', [$startdate, $enddate])->leftjoin('order','order.order_id','=','order_details.order_id')->leftjoin('products','order_details.order_product_id','=','products.id')->get();
        $titles = [
            'Start Date',
            'End Date',
            'Total Revenue',
        ];
        $excelData = [];
        $cntr = 1;
        $name = 'Revenue' . '.xlsx';
        $revenue = 0;
        foreach ($orders as $key => $data) {
            $revenue = $revenue + $data->seller_commission;
        }
        $output['Start Date'] = $request->startDate;
        $output['End Date']   = $request->endDate;
        $output['Revenue']    = $revenue;
        array_push($excelData, $output);
        return Excel::download(new OrderReportsExportByRange($titles, $excelData), $name);
    }
    function generate_excel(Request $request){
        $startdate = $request->startDate;
        $enddate = $request->endDate;
        $orders = Order_details::whereBetween('order_details.created_at', [$startdate, $enddate])->leftjoin('order','order.order_id','=','order_details.order_id')->leftjoin('products','order_details.order_product_id','=','products.id')->get();
        $titles = [
            'Sl',
            'Order Date',
            'Order Code',
            'Status',
            'Seller',
            'Customer',
            'Product',
            'QTY',
            'MRP',
            'Discount',
            'Net Price',
            'Shipping',
            'Sub Total',
            'Elesonic Commission',
            'Seller Commission',
        ];
        $excelData = [];
        $cntr = 1;
        $name = 'Order' . '.xlsx';
        foreach ($orders as $key => $data) {
            $billing = User::where('id',$data->order_customer_id)->first();
            $seller = User::where('id',$data->product_seller_id)->get();
            $output = array();
            $output['Sl'] = $cntr++;
            $output['Order Date'] = $data->created_at;
            $output['Order Code'] = $data->order_unique_id;
            $output['Status'] = $data->order_product_status;
            $output['Seller'] = @$seller[0]->name;
            $output['Customer'] = $billing->name;
            $output['Product'] = $data->name;
            $output['QTY'] = $data->cart_item_pro_qty;
            $output['MRP'] = $data->cart_item_price;
            $output['Discount'] = $data->cart_item_price_disc;
            $output['Net Price'] = $data->cart_item_net_price;
            $output['Shipping'] = $data->shipping_charge;
            $output['Sub Total'] = $data->cart_item_pro_qty * $data->cart_item_net_price;
            $output['Elesonic Commission'] = $data->admin_commission;
            $output['Commission'] = $data->seller_commission;
            
            array_push($excelData, $output);
        }
        return Excel::download(new OrderReportsExportByRange($titles, $excelData), $name);
    }

  
    
}
