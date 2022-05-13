@section('pageheader', 'Request Service')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Orders
            <small>Manage Orders</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Orders</li>
            <li class="active">Orders</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order Code</th>
                            <th>Customer</th>
                            <th>Order Amount($)</th>
                            <th>Shipping Charge($)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $('#my-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('dashboard.fetchdata', ['type' => 'orders'])}}",
                type: "POST",
                data:function( d )
                {
                    d._token = '{{csrf_token()}}';
                },
            },
            columns:[
                {
                    data:'order_id',
                    name: 'order_id',
                    render: function(data, type, full, meta){
                        return '<b class="text-primary">' + data + '</b>';
                    },
                },
                {
                    data:'order_unique_id',
                    name: 'order_unique_id',
                    render: function(data, type, full, meta){
                        return data;
                    },
                    searchable: true,
                },
                {
                    data:'name',
                    name: 'name',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'order_total_price',
                    name: 'order_total_price',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'shipping_charge',
                    name: 'shipping_charge',
                    render: function(data, type, full, meta){
                       return data;
                    },
                },
                {
                    data:'order_status',
                    name: 'order_status',
                    render: function(data, type, full, meta){
                        return data;
                    },
                },
                {
                    data:'order_status',
                    name: 'order_status',
                    render: function(data, type, full, meta){
                        return data;
                    },
                },
             
            ],
            "order": [
                [0, 'asc']
            ]
        });
    </script>
@endpush
