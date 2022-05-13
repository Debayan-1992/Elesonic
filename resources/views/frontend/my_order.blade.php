@extends('layouts.frontend.app')

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
                <th>Created On</th>
                <th>Order Id</th>
                <th>Order Price ($)</th>
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
                <td>{{$row->order_unique_id}}</td>
                <td>{{$row->order_total_price}}</td>
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