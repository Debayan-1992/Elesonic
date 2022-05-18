@extends('layouts.frontend.app')
@section('pageheader', 'Order-details')
@section('content')
<div class="container"> 
    <div class="main-body">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <div class="row">
    @include('layouts.frontend.leftpanel')

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                             
    <form action="" method="post" id="pass_form">
    <div class="col-md-5 user-card-col col-12 mb-2">
        <div id="box" class="user-card">
            <b>Billing Details</b>
            <br> 
            <b>Name</b> : {{$billingAddress->name}}<br>
            <b>Email</b> : {{$billingAddress->email}}<br>
            <b>Phone</b> : {{$billingAddress->mobile}}<br>
        </div>
    </div>
    <div class="col-md-5 user-card-col col-12 mb-2">
        <div id="box" class="user-card">
          <b>Shipping Details</b>
            <br> 
            <b>Name</b> : {{$shippingAddress->user_first_name}} {{$shippingAddress->user_last_name}}<br>
            <b>E-mail</b> : {{$shippingAddress->user_email}}<br>
            <b>Phone</b> : {{$shippingAddress->user_phone_no}}<br>
            <b>Address</b> : {{$shippingAddress->user_city}}, {{$shippingAddress->user_state}}, {{$shippingAddress->user_pincode}}
        </div>
    </div>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>Product</th>
                <th>QTY</th>
                <th>MRP($)</th>
                <th>Discount(%)</th>
                <th>Net price($)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if(count($order_details) > 0)
            @php
            $i= 0;
            @endphp
            @foreach($order_details as $row)
            @php
            $i++;
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td><img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}" height="50px" width="50px"><br><b>{{$row->name}}</b></td>
                
                <td>{{$row->cart_item_pro_qty}}</td>
                <td>{{$row->cart_item_price}}</td>
                <td>{{$row->cart_item_price_disc}}</td>
                <td>{{$row->cart_item_net_price}}</td>
                <td>{{$row->order_product_status}}</td>
            </tr>
            @endforeach
            @endif

           
            <tr>
            <!-- <td><a style="curser:pointer" href="{{$path}}" download><i class="fa fa-print" aria-hidden="true"></i></a></td> -->
            <tr>
        </tbody>
    </table>
</form>

                    </div>
                </div>
            </dIv>
        </dIv>
    </dIv>
</dIv>
@endsection