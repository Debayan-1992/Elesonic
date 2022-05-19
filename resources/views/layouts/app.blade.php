
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('pageheader', 'Dashboard') - Dashboard | {{config('app.name', 'Laravel')}} - {{config('app.title', 'Another Laravel Website')}}</title>

        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{asset('inhouse/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('inhouse/bower_components/Ionicons/css/ionicons.min.css')}}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{asset('inhouse/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{asset('inhouse/bower_components/select2/dist/css/select2.min.css')}}">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="{{asset('inhouse/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">

        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{asset('inhouse/dist/css/skins/_all-skins.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('inhouse/dist/css/AdminLTE.min.css')}}">

        <link rel="icon" type="image/png" href="{{asset('custom_resource/images/logo.png')}}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

        {{-- myPlugin CSS --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ==" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/themes/black/pace-theme-flash.min.css" integrity="sha512-0c1cb0LYXVvb9L459008ryNuWW7NuZEFY0ns6fAOfpJhHnTX7Db2vbSrjaLgvUpcl+atb3hkawh2s+eEE3KaLQ==" crossorigin="anonymous" />

        <link rel="stylesheet" href="{{asset('css/custom.css')}}"/>

        @stack('style')

        <style>
            .resendotp{
                width: fit-content;
                color: #ffffff !important;
                border-radius: 0px;
                pointer-events: all;
            } .datepicker{
                z-index: 9999 !important;
            } hr.short{
                margin-top: 10px;
                margin-bottom: 10px;
            } a.label.my-label {
                pointer-events: none;
                text-align: left;
                font-weight: bold;
            } tbody td{
                vertical-align: middle !important;
            }
        </style>
    </head>

    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            @include('inc.header')

            @include('inc.sidebar')

            <div class="content-wrapper">
                @yield('content')
            </div>

            @include('inc.footer')

            <div class="control-sidebar-bg"></div>

            <div class="modal fade-in" id="mobileverifymodal">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Verify mobile number</h4>
                        </div>

                        <form action="{{route('dashboard.profile')}}" method="POST" id="mobileverifyform" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="verifymobile">

                            <div class="modal-body">
                                <div class="form-group has-feedback">
                                    <label>OTP</label>
                                    <input type="password" value="" name="otp" class="form-control" placeholder="Enter the OTP sent to you" required>
                                    <button type="button" class="btn btn-primary form-control-feedback resendotp" id="resendmverifyotp"><i class="fa fa-lock"></i>&nbsp;&nbsp;Send OTP</button>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery 3 -->
        <script src="{{asset('inhouse/bower_components/jquery/dist/jquery.min.js')}}"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{asset('inhouse/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        <!-- SlimScroll -->
        <script src="{{asset('inhouse/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{asset('inhouse/bower_components/fastclick/lib/fastclick.js')}}"></script>
        <!-- DataTables -->
        <script src="{{asset('inhouse/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('inhouse/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
        <!-- CK Editor -->
        <script src="{{asset('inhouse/bower_components/ckeditor/ckeditor.js')}}"></script>
        <!-- Select2 -->
        <script src="{{asset('inhouse/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
        <!-- bootstrap datepicker -->
        <script src="{{asset('inhouse/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

        <!-- AdminLTE App -->
        <script src="{{asset('inhouse/dist/js/adminlte.min.js')}}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{asset('inhouse/dist/js/demo.js')}}"></script>

        {{-- myPlugins JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/pace.min.js" integrity="sha512-t3TewtT7K7yfZo5EbAuiM01BMqlU2+JFbKirm0qCZMhywEbHZWWcPiOq+srWn8PdJ+afwX9am5iqnHmfV9+ITA==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous"></script>

        <script src="{{asset('js/custom.js')}}"></script>

        @stack('script')

        <script>
            $(document).ready(function () {
                $('.sidebar-menu').tree();

                // $('#datepicker').datepicker({
                //     autoclose: true
                // })
            })

            $(function () {
                if($('#ck-editor').text()){
                    CKEDITOR.replace('ck-editor');
                }
            });

            /** @if(Auth::user()->mobile != null && Auth::user()->mobile_verified_at == null) **/
                $('#mobileverifymodal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#mobileverifymodal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#mobileverifyform').validate({
                    rules: {
                        otp: {
                            required: true,
                            number: true,
                            minlength: 6,
                            maxlength: 6,
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
                        var form = $('#mobileverifyform');

                        Pace.track(function(){
                            form.ajaxSubmit({
                                dataType:'json',
                                beforeSubmit:function(){
                                    form.find('button[type="submit"]').button('loading');
                                },
                                success:function(data){
                                    notify(data.status, 'success');
                                    location.reload();
                                },
                                error: function(errors) {
                                    form.find('button[type="submit"]').button('reset');
                                    showErrors(errors, form);
                                }
                            });
                        });
                    }
                });

                $('#resendmverifyotp').on('click', function(){
                    Pace.track(function(){
                        $.ajax({
                            url: "{{route('dashboard.profile')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'verifymobile','otp':'send'},
                            success: function(data){
                                resendotptimer(120, 'resendmverifyotp');
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                    });
                });

                function resendotptimer(remaining, buttonid) {
                    $('#'+buttonid).attr('disabled', 'true');

                    var m = Math.floor(remaining / 60);
                    var s = remaining % 60;

                    m = m < 10 ? '0' + m : m;
                    s = s < 10 ? '0' + s : s;

                    document.getElementById(buttonid).innerHTML = '<i class="fa fa-clock-o"></i>&nbsp;&nbsp;' + m + ':' + s;
                    remaining -= 1;

                    if(remaining >= 0) {
                        setTimeout(function() {
                            resendotptimer(remaining, buttonid);
                        }, 1000);
                        return;
                    }

                    document.getElementById(buttonid).innerHTML = '<i class="fa fa-repeat"></i>&nbsp;&nbsp;Resend OTP';
                    $('#'+buttonid).removeAttr('disabled');
                }
            /** @endif **/

            $('#searchform').on('submit', function(){
                $('#my-datatable').dataTable().api().ajax.reload();
            });
        </script>

        @include('inc.messages')
    </body>
</html>
