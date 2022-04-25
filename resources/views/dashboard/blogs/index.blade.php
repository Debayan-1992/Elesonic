@section('pageheader', 'Blogs')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Blogs Management
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Blogs Management</li>
            <li class="active">View All</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">All Blogs</h3>

                <div class="box-tools pull-right">
                    @if(Myhelper::can('add_blog'))
                        <a href="{{route('dashboard.blogs.add')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New</a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            {{-- <th>Blog Image</th> --}}
                            <th>Title</th>
                            <th>Created By</th>
                            <th>Last Updated</th>
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
                url: "{{route('dashboard.fetchdata', ['type' => 'blogs'])}}",
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
                // {
                //     data:'avatar',
                //     name: 'avatar',
                //     render: function(data, type, full, meta){
                //         return '<img src="' + data + '" style="width: 30px">';
                //     },
                //     orderable: false,
                //     searchable: false,
                // },
                {
                    data:'title',
                    name: 'title',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'author',
                    name: 'author',
                    render: function(data, type, full, meta){
                        return data.name
                    },
                    orderable: false,
                    searchable: false,
                },
                {
                    data:'updated_at',
                    name: 'updated_at',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'status',
                    name: 'status',
                    render: function(data, type, full, meta){
                        if(data == 1){
                            html = `<a onclick="changeAction(`+full.id+`)" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i>&nbsp;Active</a>`;
                        } else{
                            html = `<a onclick="changeAction(`+full.id+`)" class="btn btn-sm btn-warning"><i class="fa fa-remove"></i>&nbsp;Inactive</a>`;
                        }

                        return html;
                    },
                },
                {
                    render: function(data, type, full, meta){
                        var html = '';

                        @if(Myhelper::can('edit_blog'))
                            html += `<li><a href="{{route('dashboard.blogs.edit')}}/`+full.id+`"><i class="fa fa-edit"></i>Edit</a></li>`;
                        @endif

                        @if(Myhelper::can('delete_blog'))
                            html += `<li><a href="javascript:;" onclick="deleteitem('`+full.id+`')"><i class="fa fa-trash"></i>Delete</a></li>`;
                        @endif

                        var menu = `<div class="btn-group">\
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">\
                                    <i class="fa fa-bars"></i>&nbsp;&nbsp;<span class="fa fa-caret-down"></span>\
                                </button>\
                                <ul class="dropdown-menu">\
                                    `+html+`
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

        function changeAction(id){
            @if(!Myhelper::can('edit_blog'))
                return false;
            @endif

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.blogs.submit')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','operation':'changeaction','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        function deleteitem(id){
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                    Pace.track(function(){
                        $.ajax({
                            url: "{{route('dashboard.blogs.submit')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','operation':'delete','id':id},
                            success: function(data){
                                $('#my-datatable').dataTable().api().ajax.reload();
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                    });
                } else {
                    swal({
                        title: "Cancelled Successfully",
                        text: "Your data is safe!",
                        icon: "warning",
                    });
                }
            });
        }
    </script>
@endpush
