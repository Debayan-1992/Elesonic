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
                <h3 class="box-title">Service Payment History</h3>

                
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Service Name</th>
                            <th>Price</th>
                            <th>Charge_Id</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade-in" id="servicemodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Service</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.service.submit')}}" method="POST" id="serviceform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="operation" value="">
                     <input type="hidden" name="hidimage" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" class="form-control" placeholder="Enter Service Name" required>
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
                            <label>Popular <span class="text-danger">*</span></label>
                            <select name="popular" id="popular" class="form-control select2" style="width: 100%">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
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
                url: "{{route('dashboard.fetchdata', ['type' => 'service_payment_history'])}}",
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
                    data:'email',
                    name: 'email',
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
                    data:'amount',
                    name: 'amount',
                    render: function(data, type, full, meta){
                        return data
                    },
                    searchable: true,
                },
                {
                    data:'charge_id',
                    name: 'charge_id',
                    render: function(data, type, full, meta){
                        return data
                    },
                },
                {
                    data:'status',
                    name: 'status',
                    render: function(data, type, full, meta){
                        return data
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
                    data:'receipt_url',
                    name: 'receipt_url',
                    render: function(data, type, full, meta){
                        return '<a class="btn btn-primary" href='+data+'>Download</a>';
                        
                    },
                }
            ],
            "order": [
                [0, 'asc']
            ]
        });

        $('#serviceform').validate({ //onSubmit
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
                var form = $('#serviceform');

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
                             $('#servicemodal').modal('hide');
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
                    url: "{{route('dashboard.fetchdata', ['type' => 'services', 'fetch' => 'single'])}}" + "/" + id, //This hits to fetchdata fcn of CommonController
                    data: {'token':'{{csrf_token()}}'},
                    success: function(data){
                        var result = data.result;
                        //console.log(result);return false;

                        $('#servicemodal').find('.modal-title').text('Edit Banner');
                        $('#servicemodal').find('[name=id]').val(id);
                        $('#servicemodal').find('[name=operation]').val('edit');
                        $('#servicemodal').find('[name=name]').val(result.name);
                        $('#servicemodal').find('[name=slug]').val(result.slug);
                        $('#servicemodal').find('[name=description]').val(result.description);
                        $('#servicemodal').find('[name=hidimage]').val(result.image);
                        //$('#bannermodal').find('[name=type]').val(result.type);
                        $('#servicemodal').find('#status').val(result.status).trigger('change'); //This is not an array
                        $('#servicemodal').modal('show');
                    }, error: function(errors){
                        showErrors(errors, form);
                    }
                });
            });
        }

        function add(){ //when creating record
            $('#servicemodal').find('.modal-title').text('Add New Service');
            $('#servicemodal').find('[name=id]').val('');
            $('#servicemodal').find('[name=operation]').val('new');
            $('#servicemodal').find('#status').val('').trigger('change');
            $('#servicemodal').modal('show');
        }

        

        function changeAction(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.service.statusChange')}}",
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

        function popular_st_change(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.service.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'popular_st_change','id':id},
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
                    url: "{{route('dashboard.service.statusChange')}}",
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
