@section('pageheader', 'Department')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Departments
            <small>Manage Department</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Tools</li>
            <li class="active">Departments</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Departments</h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</button>
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
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

    <div class="modal fade-in" id="departmentmodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Department</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.department.submit')}}" method="POST" id="departmentform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="operation" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" class="form-control" placeholder="Enter Department Name" required>
                        </div>

                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" value="" name="slug" class="form-control" placeholder="Enter slug" required>
                        </div>

                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <input type="text" value="" name="description" class="form-control" placeholder="Enter Service Description" required>
                        </div>

                        <div class="form-group">
                            <label>Image <span class="text-danger">*</span></label>
                            <input name="image" accept="image/*" class="form-control" type="file">
                        </div>
                        
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control select2" multiple style="width: 100%">
                                @foreach ($statuses as $status)
                                    <option value="{{$status}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#my-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('dashboard.fetchdata', ['type' => 'departments'])}}",
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
                    data:'slug',
                    name: 'slug',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'description',
                    name: 'description',
                    render: function(data, type, full, meta){
                        return `<b class="text-primary" style="text-transform: capitalize">`+data+`</b>`
                    },
                    searchable: false,
                    orderable: false,
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
                    render: function(data, type, full, meta){ //Edit button
                        var html = '';

                        var menu = `<div class="btn-group">\
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">\
                                    <i class="fa fa-bars"></i>&nbsp;&nbsp;<span class="fa fa-caret-down"></span>\
                                </button>\
                                <ul class="dropdown-menu">\
                                    <li><a href="javascript:;" onclick="edit('`+full.id+`')"><i class="fa fa-edit"></i>Edit</a></li>\
                                    <li><a href="javascript:;" onclick="delet('`+full.id+`')"><i class="fa fa-trash"></i>Delete</a></li>\
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

        $('#departmentform').validate({ //onSubmit
            rules: {
                name: {
                    required: true,
                },
                description: {
                    required: true,
                },
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function() {
                var form = $('#departmentform');

                Pace.track(function(){ //Progress bar loading
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            notify(data.status, 'success');
                            // form[0].reset();
                            // form.find('#status').val('').trigger('change');
                            // $('#bannermodal').modal('hide');
                            form.find('button[type="submit"]').button('reset');
                            $('#my-datatable').dataTable().api().ajax.reload();
                        },
                        error: function(errors) {
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });

        function edit(id){ //Edit Page
            Pace.track(function(){ //Progress bar loading
                $.ajax({
                    url: "{{route('dashboard.fetchdata', ['type' => 'departments', 'fetch' => 'single'])}}" + "/" + id, //This hits to fetchdata fcn of CommonController
                    data: {'token':'{{csrf_token()}}'},
                    success: function(data){
                        var result = data.result;
                        //console.log(result);return false;

                        $('#departmentmodal').find('.modal-title').text('Edit Banner');
                        $('#departmentmodal').find('[name=id]').val(id);
                        $('#departmentmodal').find('[name=operation]').val('edit');
                        $('#departmentmodal').find('[name=name]').val(result.name);
                        $('#departmentmodal').find('[name=slug]').val(result.slug);
                        $('#departmentmodal').find('[name=description]').val(result.description);
                        //$('#bannermodal').find('[name=type]').val(result.type);
                        $('#departmentmodal').find('#status').val(result.status).trigger('change'); //This is not an array
                        $('#departmentmodal').modal('show');
                    }, error: function(errors){
                        showErrors(errors, form);
                    }
                });
            });
        }

        function add(){ //when creating record
            $('#departmentmodal').find('.modal-title').text('Add New Service');
            $('#departmentmodal').find('[name=id]').val('');
            $('#departmentmodal').find('[name=operation]').val('new');
            $('#departmentmodal').find('#status').val('').trigger('change');
            $('#departmentmodal').modal('show');
        }

        

        function changeAction(id){
            if(this.value = 'status')
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.department.statusChange')}}",
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

        function delet(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.department.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'delet','id':id},
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
