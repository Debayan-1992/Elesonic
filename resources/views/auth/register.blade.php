@section('pageheader', 'Register')

@extends('layouts.auth')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('index')}}"><b>Larav</b>IRST</a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">Signup to access our portal</p>

            <form id="registerform" action="{{route('register')}}" method="post">
                @csrf

                <div id="inputfields">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="name" placeholder="Name">
                        <span class="fa fa-user form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                        <span class="fa fa-envelope form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="nunber" class="form-control" name="mobile" placeholder="Mobile">
                        <span class="fa fa-mobile form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="new-password">
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Password Confirmation" autocomplete="new-password">
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label> <input name="terms_and_conditions" type="checkbox">&nbsp;&nbsp;Accept Terms & Conditions</label>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                    </div>
                </div>
            </form>

            <br>

            <a href="{{route('login')}}" class="text-center">Signin to Coninue</a>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });

            @if(Session::has('registerdata'))
                @php
                    $regisgterdata = session('registerdata');
                    $mobile = $regisgterdata['mobile'];
                @endphp

                $('#registerform').find('[name=mobile]').val('{{$mobile}}');
                otpsent('Please enter the OTP sent to your mobile number.');
            @endif
        });

        $('#registerform').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    number: true,
                    maxlength: 10,
                    minlength: 10,
                    required: true,
                },
                password: {
                    required: true,
                },
                password_confirmation: {
                    required: true,
                },
                // acceptterms: {
                //     required: true,
                // },
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
                var form = $('#registerform');
                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){

                            if(data.statuscode == 'OTPSENT'){
                                otpsent(data.status);
                                form.find('button[type="submit"]').button('reset');
                            } else if(data.statuscode == 'TXN'){
                                notify(data.status, 'success');
                                form[0].reset();
                                window.location.href = "{{route('dashboard.home')}}";
                                // form.find('button[type="submit"]').button('reset');
                            }
                        },
                        error: function(errors) {
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });

        function resentregotp(){
            Pace.track(function(){
                $.ajax({
                    url: "{{route('register')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','type':'resendotp'},
                    success: function(data){
                        otpsent(data.status, true);
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });
        }

        function otpsent(message, resend=false){
            form = $('#registerform');

            form.find('[name=name]').closest('div').hide();
            form.find('[name=email]').closest('div').hide();
            form.find('[name=mobile]').attr('readonly', 'true');
            form.find('[name=password]').closest('div').hide();
            form.find('[name=password_confirmation]').closest('div').hide();
            form.find('[name=terms_and_conditions]').closest('label').hide();

            if(resend == false){
                var html = `<div class="form-group has-feedback">\
                                <input type="password" class="form-control" name="otp" placeholder="OTP" autocomplete="new-password">\
                                <button type="button" class="btn btn-primary form-control-feedback resendotp" id="resendotp"><i class="fa fa-lock"></i></button>\
                            </div>`;

                form.find('#inputfields').append(html);
            }

            resendotptimer(120);
            notify(message, 'success');
        }

        function resendotptimer(remaining) {
            $('#resendotp').attr('disabled', 'true');

            var m = Math.floor(remaining / 60);
            var s = remaining % 60;

            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;

            document.getElementById('resendotp').innerHTML = '<i class="fas fa-clock"></i>&nbsp;&nbsp;' + m + ':' + s;
            remaining -= 1;

            if(remaining >= 0) {
                setTimeout(function() {
                    resendotptimer(remaining);
                }, 1000);
                return;
            }

            document.getElementById('resendotp').innerHTML = '<i class="fa fa-repeat"></i>&nbsp;&nbsp;Resend OTP';
            $('#resendotp').attr('onclick', 'resentregotp()');
            $('#resendotp').removeAttr('disabled');
        }
    </script>
@endpush


@push('style')
    <style>
        .resendotp{
            width: fit-content;
            color: #ffffff !important;
            border-radius: 0px;
            pointer-events: all;
        }
    </style>
@endpush
