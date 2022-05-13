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
                <td>{{$row->name}}</td>
                <td>{{$row->cart_item_pro_qty}}</td>
                <td>{{$row->cart_item_price}}</td>
                <td>{{$row->cart_item_price_disc}}</td>
                <td>{{$row->cart_item_net_price}}</td>
                <td>{{$row->order_product_status}}</td>
            </tr>
            @endforeach
            @endif
          
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