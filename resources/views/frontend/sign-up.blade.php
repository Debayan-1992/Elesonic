@extends('layouts.frontend.app')

@push('header')
  <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/plugins/iCheck/square/blue.css">
{{--@endpush--}}
@endpush


@section('content')

<!-- sign-up -->
<div class="login-block">
    <div class="container">

      <!---->
        <div class="login_bd-sec">
          <!---->
          <div class="lt-sec">
            <form id="signupform" action="{{route('signup_post')}}" method="post">
              @csrf
              <h4>Sign Up Account</h4>
              <!-- item -->
              <div class="user-select-area">
                <input type="radio" id="customer" name="role_id" value="5">
               <label for="">Customer</label><br>
               <input type="radio" id="seller" name="role_id" value="6">
               <label for="">Seller</label><br>

              </div>
              <div class="item">
				<div class="icon">
					<i class="fa fa-user-o"></i>
				</div>
                <input type="text" placeholder="Name" name="name">
              </div>
              <!-- item -->

              <!-- item -->
              <div class="item">

				<div class="icon">
					<i class="fa fa-phone"></i>
				</div>

                <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Phone No" name="mobile">
              </div>
              <!-- item -->

              <!-- item -->
              <div class="item">
              	<div class="icon">
					<i class="fa fa-envelope-o"></i>
				</div>
                <input type="email" placeholder="Email" name="email">
              </div>
              <!-- item -->


              <!-- item -->
              <div class="item">
              	<div class="icon">
              		<i class="fa fa-lock"></i>
              	</div>
                <input type="password" placeholder="Password" name="password">
              </div>
              <!-- item -->

              <!-- item -->
              <div class="item">
              	<div class="icon">
              		<i class="fa fa-lock"></i>
              	</div>
                <input type="password" placeholder="Confirm password" name="password_confirmation">
              </div>
              <!-- item -->

              <!-- item -->
                <div class="item">
                  <input type="submit" value="submit">
                </div>
              <!-- item -->
            </form>
          </div>
          <!---->
          <!--====-->
          <div class="rt-sec">
            <div class="rt_body">
            	<p>Enter your id and password to continue ?</p>
              <a href="{{route('login')}}">Account Login</a>
            </div>
          </div>
          <!---->
          <div class="clearfix"></div>
        </div>
      <!---->
    </div>
</div>

<!---->

<!-- login -->

@endsection


@push('script')
  

  <script src="https://adminlte.io/themes/AdminLTE/plugins/iCheck/icheck.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/pace.min.js" integrity="sha512-t3TewtT7K7yfZo5EbAuiM01BMqlU2+JFbKirm0qCZMhywEbHZWWcPiOq+srWn8PdJ+afwX9am5iqnHmfV9+ITA==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous"></script>

  <script src="{{asset('js/custom.js')}}"></script>
@endpush

@push('script')

    <script>
        $(function () {
            $('input').iCheck({ 
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        });

        $('#signupform').validate({
            rules: {
              role_id: {
                    required: true,
                },
              name: {
                    required: true,
                },
                mobile: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: "Please enter your email address",
                    email: "The inserted email address must be a email"
                },
                password: {
                    required: "Please enter a password to continue"
                }
            },
            // errorElement: "p",
            // errorPlacement: function ( error, element ) {
            //     if ( element.prop("tagName").toLowerCase() === "select" ) {
            //         error.insertAfter( element.closest( ".form-group" ).find(".select2") );
            //     } else {
            //         error.insertAfter( element );
            //     }
            // },
            submitHandler: function() {
                var form = $('#signupform');
                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            //form.find('button[type="submit"]').button('loading');
                            $("#loadList").css('display','block');
                        },
                        success:function(data){
                            // form.find('button[type="submit"]').button('reset');

                            form[0].reset();
                            notify(data.status, 'success');
                            $("#loadList").css('display','none');
                            window.location.href = "{{route('customer.customer_dashboard')}}";
                        },
                        error: function(errors) {
                            //form.find('button[type="submit"]').button('reset');
                            $("#loadList").css('display','none');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });
    </script>
@endpush
