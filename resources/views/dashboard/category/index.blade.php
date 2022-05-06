@section('pageheader', 'Categories')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
           Categories
        </h1>
     
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Categories</h3>

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

    <div class="modal fade-in" id="categorymodal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.category.store')}}" method="POST" id="categoryform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="operation" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" class="form-control" placeholder="Enter Name" required>
                        </div>

                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" value="" onkeyup="slugname(this.value)" id="slug" name="slug" class="form-control" placeholder="Enter Slug" required>
                        </div>

                        <div class="form-group">
                            <label>Parent</label>
                            <select class="select2 form-control" name="parent_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">

                            <option value="0">No Parent</option>

                            @foreach ($categories as $category)

                                <option value="{{ $category->id }}">{{ $category->name }}</option>

                                @foreach ($category->childrenCategories as $childCategory)

                                @include('categories.child_category', ['child_category' => $childCategory,'cat_id' =>''])

                               @endforeach

                            @endforeach

                        </select>
                        </div>


                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea  name="description" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text"  name="meta_title" class="form-control" placeholder="Meta Title">
                           
                        </div>
                        <div class="form-group">
                            <label>Meta Keyword</label>
    
                            <input type="text"  name="meta_keyword" class="form-control" placeholder="Meta Keyword">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea  name="meta_description" class="form-control"></textarea>
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
    <script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    function slugname(value){
            var value = value;
            var new_value =value.toLowerCase();
            var new_value =new_value.replace(/ /g, "-");
            $("#slug").val(new_value);
        }
    <script>
        function edit(id){
            var url = '{{ route("dashboard.category.edit", ":slug") }}';
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
                            url: "{{route('dashboard.category.statusChange')}}",
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
        function changeAction(id){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.category.statusChange')}}",
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
                url: "{{route('dashboard.fetchdata', ['type' => 'category'])}}",
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

        $('#categoryform').validate({
            rules: {
                name: {
                    required: true,
                },
                slug: {
                    required: true,
                },
                description: {
                    required: true,
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
                var form = $('#categoryform');

                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            notify(data.status, 'success');
                            // form[0].reset();
                            // form.find('#role_id').val('').trigger('change');
                            $("form")[0].reset();
                            $('#categorymodal').modal('hide');
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
        function add(){
            $('#categorymodal').find('.modal-title').text('Add New Category');
            $('#categorymodal').find('[name=id]').val('');
            $('#categorymodal').find('[name=operation]').val('new');
            $('#categorymodal').find('#role_id').val('').trigger('change');
            $('#categorymodal').modal('show');
        }
    </script>
@endpush
