@section('pageheader', $role->name)
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Management
            <small>Manage {{$role->name}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Customer Management</li>
            <li class="active">{{$role->name}}</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">All {{$role->name}}s</h3>

                <div class="box-tools pull-right">
                    @if(Myhelper::can('add_'.$role->slug))
                        <a href="{{route('dashboard.members.add', ['type' => $type])}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New</a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Created At</th>
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
                url: "{{route('dashboard.fetchdata', ['type' => $type])}}",
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
                    data:'name',
                    name: 'name',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'email',
                    name: 'email',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'mobile',
                    name: 'mobile',
                    render: function(data, type, full, meta){
                        if(data != null){
                            return data;
                        } else{
                            return 'N/A';
                        }
                    },
                },
                {
                    data:'created_at',
                    name: 'created_at',
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

                        @if(Myhelper::can('edit_'.$role->slug))
                            html += `<li><a href="{{route('dashboard.profile')}}/`+btoa(full.id)+`"><i class="fa fa-edit"></i>Edit</a></li>`;
                            html += `<li><a href="{{route('dashboard.members.permission')}}/`+btoa(full.id)+`"><i class="fa fa-lock"></i>Permissions</a></li>`;
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
            // @if(!Myhelper::can('edit_'.$role->type))
            //     return false;
            // @endif

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.members.changeaction')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','role':'{{$role->slug}}','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }
    </script>
@endpush
