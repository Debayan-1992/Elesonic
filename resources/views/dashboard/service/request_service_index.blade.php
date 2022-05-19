@section('pageheader', 'Request Service')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Services
            <small>Manage Service</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Tools</li>
            <li class="active">Services</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Request Service</h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</button>
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Customer Phone</th>
                            <th>Requested On</th>
                            <th>Req Acc Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade-in" id="r_servicemodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>Service Acceptance Form</b></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.request_service.r_service_submit')}}" method="POST" id="r_serviceform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="serviceBookingId" id="serviceBookingId" value="">
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Price <span class="text-danger">*</span></label>
                            <input type="text" value="" name="service_offered_price" class="form-control" placeholder="Enter Service Price" required>
                        </div>

                        <div class="form-group">
                            <label>Message <span class="text-danger">*</span></label>
                            <textarea type="text" value="" name="message" class="form-control" placeholder="Enter Message" required></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Send Payment Link</button>
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
                url: "{{route('dashboard.fetchdata', ['type' => 'request_service'])}}",
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
                    data:'service_name',
                    name: 'service_name',
                    render: function(data, type, full, meta){
                        return data
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
                    data:'email',
                    name: 'email',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'phone',
                    name: 'phone',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'created_at',
                    name: 'created_at',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'service_acceptance_status',
                    name: 'service_acceptance_status',
                    render: function(data, type, full, meta){
                        if(data == 'P'){
                            html = `<a class="btn btn-sm btn-warning"><i class="fa fa-check-circle"></i>&nbsp;Pending</a>`;
                        } else if(data== 'A'){
                            html = `<a class="btn btn-sm btn-success"><i class="fa fa-remove"></i>&nbsp;Accepted</a>`;
                        }else if(data== 'I'){
                            html = `<a class="btn btn-sm btn-danger"><i class="fa fa-remove"></i>&nbsp;Rejected</a>`;
                        }
                        else{
                            html = '';
                        }

                        return html;
                    },
                },
                {
                    data:'service_acceptance_status',
                    name: 'service_acceptance_status',
                    render: function(data, type, full, meta){
                        var html = '';
                        if(data == 'P'){
                        var menu = `<div class="btn-group">\
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">\
                                    <i class="fa fa-bars"></i>&nbsp;&nbsp;<span class="fa fa-caret-down"></span>\
                                </button>\
                                <ul class="dropdown-menu">\
                                    <li><a href="javascript:;" onclick="accept('`+full.id+`')"><i class="fa fa-edit"></i>Accept</a></li>\
                                    <li><a href="javascript:;" onclick="reject('`+full.id+`')"><i class="fa fa-trash"></i>Reject</a></li>\
                                </ul>\
                                
                            </div>`;
                        }else if(data == 'A'){
                            menu = `<a class="btn btn-sm btn-success"><i class="fa fa-remove"></i>&nbsp;Accepted</a>`;
                        }else if(data== 'I'){
                            menu = `<a class="btn btn-sm btn-danger"><i class="fa fa-remove"></i>&nbsp;Rejected</a>`;
                        }else{ menu=''; }
    

                        return menu;
                    },
                },
                
            ],
            "order": [
                [0, 'asc']
            ]
        });

        $('#r_serviceform').validate({ //onSubmit
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
                var form = $('#r_serviceform');

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
                             $('#r_servicemodal').modal('hide');
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

        function accept(id){ //Edit Page
            $("#serviceBookingId").val(id);
            $('#r_servicemodal').modal('show');
        }

        function reject(id){
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
                            url: "{{route('dashboard.request_service.statusChange')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'delete','id':id},
                            success: function(data){
                                $('#my-datatable').dataTable().api().ajax.reload();
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                    });       // submitting the form when user press yes
				} else {}
			});
        }
        
    </script>
@endpush
