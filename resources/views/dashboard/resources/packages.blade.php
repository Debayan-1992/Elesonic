@section('pageheader', 'Membership Packages')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Membership Packages
            <small>Manage {{$role->name}} Packages</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Resources</li>
            <li class="">Membership Packages</li>
            <li class="active">{{$role->name}}</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Membership Plans <small>{{$role->name}}</small></h3>

                <div class="box-tools pull-right">
                    @if(Myhelper::can('add_bank_membership_package'))
                        <button class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</button>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Slug</th>
                            <th>Name</th>
                            <th>Purcahse Price</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade-in" id="planmodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Profile Picture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.resources.packagesubmit')}}" method="POST" id="planform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="role_id" value="">
                    <input type="hidden" name="type" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" value="" name="slug" class="form-control" placeholder="Unique & non-editable">
                        </div>

                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" class="form-control" placeholder="Enter Plan Name">
                        </div>

                        <div class="form-group">
                            <label>Original Price <span class="text-danger">*</span></label>
                            <input type="text" value="" name="original_price" class="form-control" placeholder="Enter Original Price">
                        </div>

                        <div class="form-group">
                            <label>Offered Price</label>
                            <input type="text" value="" name="offered_price" class="form-control" placeholder="Enter Offered Price">
                        </div>

                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" placeholder="Enter description for the plan"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Validity (In days) <span class="text-danger">*</span></label>
                            <input type="text" value="" name="validity" class="form-control" placeholder="In days">
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
                url: "{{route('dashboard.fetchdata', ['type' => 'membershipplans'])}}",
                type: "POST",
                data:function( d )
                {
                    d._token = '{{csrf_token()}}';
                    d.role_id = '{{$role->id}}';
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
                    data:'slug',
                    name: 'slug',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'name',
                    name: 'name',
                    render: function(data, type, full, meta){
                        html = data;

                        if(full.featured == 1){
                            html += `&nbsp;<a onclick="changeFeatured(`+full.id+`)" href="javascript:void(0);" class="text-yellow"><i class="fa fa-star"></i></a>`;
                        } else{
                            html += `&nbsp;<a onclick="changeFeatured(`+full.id+`)" href="javascript:void(0);" class="text-yellow"><i class="fa fa-star-o"></i></a>`;
                        }

                        return html;
                    },
                    searchable: true,
                },
                {
                    data:'purchase_price',
                    name: 'purchase_price',
                    render: function(data, type, full, meta){
                        return `<b class="text-primary">{!!config('app.currency.icon')!!}`+data+`</b>`
                    },
                    searchable: false,
                    orderable: false,
                },
                {
                    data:'validity',
                    name: 'validity',
                    render: function(data, type, full, meta){
                        return `<b class="text-danger">`+data+` days</b>`
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
                    data:'updated_at',
                    name: 'updated_at',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    render: function(data, type, full, meta){
                        var html = '';

                        /* @if(Myhelper::can('edit_'.$role->slug.'_membership_package')) */
                            html += `<li><a href="javascript:;" onclick="edit('`+full.id+`')"><i class="fa fa-edit"></i>Edit</a></li>`;
                        /* @endif */

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

        $('#planform').validate({
            rules: {
                name: {
                    required: true,
                },
                original_price: {
                    required: true,
                    number: true,
                },
                description: {
                    required: true,
                },
                validity: {
                    required: true,
                    number: true,
                },
                offered_price: {
                    number: true,
                }
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
                var form = $('#planform');

                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            notify(data.status, 'success');
                            form[0].reset();
                            form.find('button[type="submit"]').button('reset');
                            $('#my-datatable').dataTable().api().ajax.reload();
                            $('#planmodal').modal('hide');
                        },
                        error: function(errors) {
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });

        function edit(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.fetchdata', ['type' => 'membershipplans', 'fetch' => 'single'])}}" + "/" + id,
                    data: {'token':'{{csrf_token()}}'},
                    success: function(data){
                        var result = data.result;

                        $('#planmodal').find('.modal-title').html('Edit Plan <small>{{$role->name}}</small>');
                        $('#planmodal').find('[name=type]').val('edit');
                        $('#planmodal').find('[name=id]').val(id);
                        $('#planmodal').find('[name=slug]').val(result.slug);
                        $('#planmodal').find('[name=slug]').attr('disabled', true);
                        $('#planmodal').find('[name=role_id]').val('{{$role->id}}');
                        $('#planmodal').find('[name=name]').val(result.name);
                        $('#planmodal').find('[name=original_price]').val(result.original_price);
                        $('#planmodal').find('[name=offered_price]').val(result.offered_price);
                        $('#planmodal').find('[name=discounted_price]').val(result.discounted_price);
                        $('#planmodal').find('[name=description]').text(result.description);
                        $('#planmodal').find('[name=validity]').val(result.validity);
                        $('#planmodal').modal('show');
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        function add(){
            $('#planmodal').find('.modal-title').html('Add New Plan <small>{{$role->name}}</small>');
            $('#planmodal').find('[name=id]').val('');
            $('#planmodal').find('[name=slug]').val('');
            $('#planmodal').find('[name=slug]').removeAttr('disabled');
            $('#planmodal').find('[name=role_id]').val('{{$role->id}}');
            $('#planmodal').find('[name=type]').val('new');
            $('#planmodal').find('[name=name]').val('');
            $('#planmodal').find('[name=original_price]').val('');
            $('#planmodal').find('[name=offered_price]').val('');
            $('#planmodal').find('[name=description]').text('');
            $('#planmodal').find('[name=validity]').val('');
            $('#planmodal').modal('show');
        }

        function changeAction(id){
            /* @if(!Myhelper::can('edit_'.$role->slug.'_membership_package')) */
                return false;
            /* @endif */

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.resources.packagesubmit')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'changeaction','id':id},
                    success: function(data){
                        $('#my-datatable').dataTable().api().ajax.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        function changeFeatured(id){
            /* @if(!Myhelper::can('edit_'.$role->slug.'_membership_package')) */
                return false;
            /* @endif */

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.resources.packagesubmit')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'changefeatured','id':id},
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
