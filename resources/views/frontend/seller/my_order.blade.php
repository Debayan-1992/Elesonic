@extends('layouts.frontend.app')
@section('pageheader', 'Orders')
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
    <table id="example" class="table table-bordered table-striped" style="width: 100%">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>Created On</th>
                <th>Order Code</th>
                <th>Product</th>
                <th>Order Total Price($)</th>
                <th>Commission($)</th>
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            @if(count($orders) > 0)
            @php
            $i= 0;
            @endphp
            @foreach($orders as $row)
            @php
            $i++;
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td>{{$row->created_at}}</td>
                <td><a href="{{route('seller.order-details')}}/{{ $row->order_id }}" target="_blank">{{$row->order_unique_id}}</a></td>
                <td>{{$row->name}}</td>
                <td>{{$row->order_total_price}}</td>
                <td>{{$row->seller_commission}}</td>
                <td>{{$row->order_status}}</td>
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