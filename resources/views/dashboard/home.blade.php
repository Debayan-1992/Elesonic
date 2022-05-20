@extends('layouts.app')

@section('content')

    <section class="content-header">
        <h1>
            Dashboard
            <small>Control Panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Title</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text">Total Order</span>
                        <span class="info-box-number">{{$order_total}}</span>
                        </div>
                    </div>  
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-ios-gear-outline"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text">Total Service</span>
                        <span class="info-box-number">{{$service_total}}</span>
                        </div>
                    </div>  
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text">Total Customer</span>
                        <span class="info-box-number">{{$customer_total}}</span>
                        </div>
                    </div>  
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text">Total Seller</span>
                        <span class="info-box-number">{{$seller_total}}</span>
                        </div>
                    </div>  
                </div>
                
            </div>


            <div class="box-footer">
                Footer
            </div>
        </div>
    </section>

@endsection
