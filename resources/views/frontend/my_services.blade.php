@extends('layouts.frontend.app')
@section('pageheader', 'Services')
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
                <th>Request On</th>
                <th>Service</th>
                <th>Approval</th>
                <th>Quoted Price($)</th>
                <th>Payment</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @if(count($services) > 0)
            @php
            $i= 0;
            @endphp
            @foreach($services as $row)
            @php
            $i++;
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->name}}</td>
               
                <td>
                    @if($row->service_acceptance_status == 'P')
                        Pending
                    @elseif($row->service_acceptance_status == 'A')
                        Accepted
                    @else
                        Rejected
                    @endif
                </td>
                <td>
                    @if($row->service_offered_price > 0)
                       {{$row->service_offered_price}}
                    @else
                    0
                    @endif
                </td>
                <td>Unpaid</td>
                <td>Lorem</td>
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