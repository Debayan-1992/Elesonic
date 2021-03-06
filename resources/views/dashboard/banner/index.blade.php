@section('pageheader', 'Banners')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Banners
            <small>Manage Banner</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Tools</li>
            <li class="active">Permissions</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Banners</h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</button>
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
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

    <div class="modal fade-in" id="bannermodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Banner</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.banner.submit')}}" method="POST" id="bannerform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="operation" value="">
                     <input type="hidden" name="hidimage" value="">

                    <div class="modal-body">
                        <div class="form-group" style="display: none;">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" value="" name="title" class="form-control" placeholder="Enter Banner Title" required>
                        </div>

                        <div class="form-group" style="display: none;">
                            <label>Description <span class="text-danger">*</span></label>
                            <input type="text" value="" name="description" class="form-control" placeholder="Enter Banner Description" required>
                        </div>

                        <div class="form-group">
                            <label>Picture <span class="text-danger">*</span></label>
                            <input name="image" accept="image/*" class="form-control" type="file">
                        </div>
                        
                        <div class="form-group" style="display: none;">
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
                url: "{{route('dashboard.fetchdata', ['type' => 'banners'])}}",
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
                    data:'image',
                    name: 'image',
                    render: function(data, type, full, meta){
                        return '<img src='+'{{config('app.url')}}/uploads/banners/'+data+' height="50px" width="50px">';
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

        $('#bannerform').validate({ //onSubmit
            rules: {
                title: {
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
                var form = $('#bannerform');

                Pace.track(function(){ //Progress bar loading
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            notify(data.status, 'success');
                             form[0].reset();
                            // form.find('#status').val('').trigger('change');
                             $('#bannermodal').modal('hide');
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
                    url: "{{route('dashboard.fetchdata', ['type' => 'banners', 'fetch' => 'single'])}}" + "/" + id, //This hits to fetchdata fcn of CommonController
                    data: {'token':'{{csrf_token()}}'},
                    success: function(data){
                        var result = data.result;
                        //console.log(result);return false;

                        $('#bannermodal').find('.modal-title').text('Edit Banner');
                        $('#bannermodal').find('[name=id]').val(id);
                        $('#bannermodal').find('[name=operation]').val('edit');
                        $('#bannermodal').find('[name=title]').val(result.b_title);
                        $('#bannermodal').find('[name=description]').val(result.b_description);
                        $('#bannermodal').find('[name=hidimage]').val(result.image);
                        //$('#bannermodal').find('[name=type]').val(result.type);
                        $('#bannermodal').find('#status').val(result.status).trigger('change'); //This is not an array
                        $('#bannermodal').modal('show');
                    }, error: function(errors){
                        showErrors(errors, form);
                    }
                });
            });
        }

        function add(){ //when creating record
            $('#bannermodal').find('.modal-title').text('Add New Banner');
            $('#bannermodal').find('[name=id]').val('');
            $('#bannermodal').find('[name=operation]').val('new');
            $('#bannermodal').find('#status').val('').trigger('change');
            $('#bannermodal').modal('show');
        }

        

        function changeAction(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.banner.statusChange')}}",
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
                    url: "{{route('dashboard.banner.statusChange')}}",
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
