@extends('layouts.frontend.app')
@section('pageheader', 'Products')
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
    <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
        <thead>
            <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>MRP($)</th>
            <th>Discount(%)</th>
            <th>Net Price</th>
            <th>Status</th>
            <th>Is Popular</th>
            <th>Action</th> 
            </tr>
        </thead>
        <tbody>
          
          
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
@push('script')
<script>
    function edit(id){
            var url = '{{ route("seller.productedit", ":slug") }}';
            url = url.replace(':slug', id);
            window.location.href = url;
        }
     $('#my-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('seller.fetchdata', ['type' => 'product'])}}",
                type: "POST",
                data:function( d )
                {
                    d._token = '{{csrf_token()}}';
                },
            },
            columns:[
                {
                    data:'id',
                    name: 'id',
                    render: function(data, type, full, meta){
                        return '<b class="text-primary">' + data + '</b>';
                    },
                },
                {
                    data:'photos',
                    name: 'photos',
                    render: function(data, type, full, meta){
                        return '<img src='+'{{config('app.url')}}/uploads/products/'+data+' height="50px" width="50px">';
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
                    data:'purchase_price',
                    name: 'purchase_price',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'discount',
                    name: 'discount',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'unit_price',
                    name: 'unit_price',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'status',
                    name: 'status',
                    render: function(data, type, full, meta){
                        if(data == 'A'){
                            html = `<a onclick="changeAction(`+full.id+`)" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i>&nbsp;Active</a>`;
                        } else if(data== 'I'){
                            html = `<a onclick="changeAction(`+full.id+`)" class="btn btn-sm btn-warning"><i class="fa fa-remove"></i>&nbsp;Inactive</a>`;
                        }else{
                            html = '';
                        }

                        return html;
                    },
                },
                {
                    data:'ispopular',
                    name: 'ispopular',
                    render: function(data, type, full, meta){
                        if(data == 'Y'){
                            html = `<a onclick="changeActionPopular(`+full.id+`)" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i>&nbsp;Yes</a>`;
                        } else if(data== 'N'){
                            html = `<a onclick="changeActionPopular(`+full.id+`)" class="btn btn-sm btn-warning"><i class="fa fa-remove"></i>&nbsp;No</a>`;
                        }else{
                            html = '';
                        }

                        return html;
                    },
                },
                {
                    render: function(data, type, full, meta){
                        var html = '';

                        var menu = `<div class="btn-group">\
                               
                                    <a href="javascript:;" onclick="edit('`+full.id+`')"><i class="fa fa-edit"></i></a>\
                                    <a href="javascript:;" onclick="changeActionDelete(`+full.id+`)"><i class="fa fa-trash"></i></a>\
                              
                    
                            </div>`;

                        return menu;
                    },
                    orderable: false,
                    searchable: false,
                }
            ],
            "order": [
                [0, 'asc']
            ]
        });

        function changeActionDelete(id){
            swal({
			 title: "Are you sure?",
			 text: "",
			 icon: "warning",
			 buttons: true,
			 dangerMode: true,
			})
			.then((willDelete) => {
			if (willDelete) {
                    $.ajax({
                            url: "{{route('seller.statusChange')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'delete','id':id},
                            success: function(data){
                                $('#my-datatable').dataTable().api().ajax.reload();
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                       // submitting the form when user press yes
				} else {
				  }
				});
         
        }
        function changeActionPopular(id){
                $.ajax({
                    url: "{{route('seller.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'popular','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
            });
        }

        function changeAction(id){
            $.ajax({
                    url: "{{route('seller.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'statusChange','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
            });
        }
</script>
@endpush