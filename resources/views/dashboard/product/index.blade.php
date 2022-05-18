@section('pageheader', 'Product')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
        Products
        </h1>
     
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Products</h3>

                <div class="box-tools pull-right">
                    <a href="{{route('dashboard.product.create')}}" class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</a>
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Is Popular</th>
                            <th>Is Best</th>
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
    <script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function edit(id){
            var url = '{{ route("dashboard.product.edit", ":slug") }}';
            url = url.replace(':slug', id);
            window.location.href = url;
        }
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
                Pace.track(function(){
                    $.ajax({
                            url: "{{route('dashboard.product.statusChange')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'delete','id':id},
                            success: function(data){
                                $('#my-datatable').dataTable().api().ajax.reload();
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                    });       // submitting the form when user press yes
				} else {
				  }
				});
         
        }
        function changeActionPopular(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.product.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'popular','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }
        function changeActionBest(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.product.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'best','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }
        function changeAction(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.product.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'statusChange','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        $('#my-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('dashboard.fetchdata', ['type' => 'product'])}}",
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
                    data:'isbest',
                    name: 'isbest',
                    render: function(data, type, full, meta){
                        if(data == 'Y'){
                            html = `<a onclick="changeActionBest(`+full.id+`)" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i>&nbsp;Yes</a>`;
                        } else if(data== 'N'){
                            html = `<a onclick="changeActionBest(`+full.id+`)" class="btn btn-sm btn-warning"><i class="fa fa-remove"></i>&nbsp;No</a>`;
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
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">\
                                    <i class="fa fa-bars"></i>&nbsp;&nbsp;<span class="fa fa-caret-down"></span>\
                                </button>\
                                <ul class="dropdown-menu">\
                                    <li><a href="javascript:;" onclick="edit('`+full.id+`')"><i class="fa fa-edit"></i>Edit</a></li>\
                                    <li><a href="javascript:;" onclick="changeActionDelete(`+full.id+`)"><i class="fa fa-trash"></i>Delete</a></li>\
                                </ul>\
                    
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

    </script>
@endpush
