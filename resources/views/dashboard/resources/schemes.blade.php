@section('pageheader', 'Scheme Packages')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Scheme Packages
            <small>Manage {{$role->name}} Schemes</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="">Resources</li>
            <li class="">Scheme Packages</li>
            <li class="active">{{$role->name}}</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">All Schemes <small>{{$role->name}}</small></h3>

                <div class="box-tools pull-right">
                    @if(Myhelper::can('add_agent_scheme'))
                        <button class="btn btn-primary btn-sm" onclick="add()"><i class="fa fa-plus"></i> Add New</button>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
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

    <div class="modal fade-in" id="schememodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Profile Picture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.resources.schemesubmit')}}" method="POST" id="schemeform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="role_id" value="">
                    <input type="hidden" name="type" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" class="form-control" placeholder="Enter Scheme Name">
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

    <div class="modal fade-in" id="commissionmodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Profile Picture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.resources.commissionsubmit')}}" method="POST" class="commissionform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="scheme_id" value="">
                    <input type="hidden" name="role_id" value="">
                    <input type="hidden" name="type_id" value="">

                    <div class="modal-body">
                        <table class="table table-bordered table-striped dataTable no-footer">
                            <thead>
                                <th>Sl</th>
                                <th>Slab</th>
                                <th class="">Type</th>
                                <th class="">Value</th>
                            </thead>
                            <tbody>
                                @foreach($loanslabs as $item)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$item->name}}</td>
                                        <td class="">
                                            <input type="hidden" name="slab[]" value="{{$item->id}}">

                                            <select name="type[]" class="form-control select2" style="width: 100px">
                                                <option value="flat">Flat</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </td>
                                        <td class="">
                                            <input type="number" name="value[]" class="form-control" placeholder="Enter value" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                url: "{{route('dashboard.fetchdata', ['type' => 'schemes'])}}",
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
                    data:'name',
                    name: 'name',
                    render: function(data, type, full, meta){
                        return data;
                    },
                    searchable: true,
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

                        /* @if(Myhelper::can('edit_'.$role->slug.'_scheme')) */
                            html += `<li><a href="javascript:;" onclick="edit('`+full.id+`')"><i class="fa fa-edit"></i>Edit</a></li>`;
                        /* @endif */

                        /* @if(Myhelper::can('delete_'.$role->slug.'_scheme')) */
                            html += `<li><a href="javascript:;" onclick="deleteitem('`+full.id+`')"><i class="fa fa-trash"></i>Delete</a></li>`;
                        /* @endif */

                        html += '<li class="divider"></li><li><a class="label my-label">Commission</a></li>'
                        /* @foreach($loantypes as $item) */
                            html += `<li><a href="javascript:;" onclick="commission('`+full.id+`', '`+full.name+`', 'commissionmodal', '{{$item->id}}')">{!! config('app.currency.faicon') !!}{{$item->name}}</a></li>`;
                        /* @endforeach */

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

        $('#schemeform').validate({
            rules: {
                name: {
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
                var form = $('#schemeform');

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
                            $('#schememodal').modal('hide');
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
                    url: "{{route('dashboard.fetchdata', ['type' => 'schemes', 'fetch' => 'single'])}}" + "/" + id,
                    data: {'token':'{{csrf_token()}}'},
                    success: function(data){
                        var result = data.result;

                        $('#schememodal').find('.modal-title').html('Edit Scheme <small>{{$role->name}}</small>');
                        $('#schememodal').find('[name=type]').val('edit');
                        $('#schememodal').find('[name=id]').val(id);
                        $('#schememodal').find('[name=role_id]').val('{{$role->id}}');
                        $('#schememodal').find('[name=name]').val(result.name);
                        $('#schememodal').modal('show');
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        function add(){
            $('#schememodal').find('.modal-title').html('Add New Scheme <small>{{$role->name}}</small>');
            $('#schememodal').find('[name=id]').val('');
            $('#schememodal').find('[name=role_id]').val('{{$role->id}}');
            $('#schememodal').find('[name=type]').val('new');
            $('#schememodal').find('[name=name]').val('');
            $('#schememodal').modal('show');
        }

        function changeAction(id){
            /* @if(!Myhelper::can('edit_'.$role->slug.'_scheme')) */
                return false;
            /* @endif */

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.resources.schemesubmit')}}",
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
            /* @if(!Myhelper::can('edit_'.$role->slug.'_scheme')) */
                return false;
            /* @endif */

            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.resources.schemesubmit')}}",
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
                            url: "{{route('dashboard.resources.schemesubmit')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'delete','id':id},
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

        function commission(id, name, modal, type){
            Pace.track(function(){
                $.ajax({
                    url: '{{route("dashboard.resources.getcommission")}}',
                    type: 'POST',
                    dataType:'JSON',
                    data:{'_token':'{{csrf_token()}}','scheme_id':id,'type_id':type}
                })
                .done(function(data) {
                    $('#'+modal).find('form')[0].reset();
                    if(data.length > 0){
                        $.each(data, function(index, values) {
                            $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('select[name="type[]"]').val(values.type);
                            $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="value[]"]').val(values.value);
                        });
                    }

                    $('#'+modal).find('.modal-title').text(name+" Commission");
                    $('#'+modal).find('[name="scheme_id"]').val(id);
                    $('#'+modal).find('[name="type_id"]').val(type);
                    $('#'+modal).find('[name="role_id"]').val('{{$role->id}}');
                    $('#'+modal).modal();
                })
                .fail(function(errors) {
                    showErrors(errors);
                });
            });
        }

        $('form.commissionform').submit(function(){
            var form= $(this);
            form.closest('.modal').find('tbody').find('span.pull-right').remove();
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button[type="submit"]').button('loading');
                },
                complete: function(){
                    form.find('button[type="submit"]').button('reset');
                },
                success:function(data){
                    $.each(data.status, function(index, values) {
                        if(values.id){
                            form.find('input[value="'+index+'"]').closest('tr').find('td').eq(0).append('<span class="pull-right text-success"><i class="fa fa-check"></i></span>');
                        }else{
                            form.find('input[value="'+index+'"]').closest('tr').find('td').eq(0).append('<span class="pull-right text-danger"><i class="fa fa-times"></i></span>');
                            if(values != 0){
                                form.find('input[value="'+index+'"]').closest('tr').find('input[name="value[]"]').closest('td').append('<span class="text-danger pull-right"><i class="fa fa-times"></i> '+values+'</span>');
                            }
                        }
                    });

                    setTimeout(function () {
                        form.find('span.pull-right').remove();
                    }, 10000);
                },
                error: function(errors) {
                    form.find('button[type="submit"]').button('reset');
                    showErrors(errors, form);
                }
            });
            return false;
        });
    </script>
@endpush
