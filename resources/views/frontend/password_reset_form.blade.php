@extends('layouts.frontend.app')

@push('header')
  <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ==" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/themes/black/pace-theme-flash.min.css" integrity="sha512-0c1cb0LYXVvb9L459008ryNuWW7NuZEFY0ns6fAOfpJhHnTX7Db2vbSrjaLgvUpcl+atb3hkawh2s+eEE3KaLQ==" crossorigin="anonymous" />
{{--@endpush--}}
@endpush

@section('content')
<!-- login-block -->

<div class="login-block">
    <div class="container">
      <!---->
        <div class="login_bd-sec">
          <!---->
          <div class="lt-sec">
            <form action="{{route('resetPassword')}}" method="post" id="loginform">
              @csrf

              <div class="form-group row">
                <label for="email_addr" class="col-md-4 col-form-label text-md-right" readonly hidden>E-Mail</label>
                
                <div class="col-md-6">
                    <input class="form-control" name="email" type="text" value="{{$email}}" readonly hidden>
                </div>
                <div class="col-md-6">
                    <input class="form-control" name="token" type="text" value="{{$token}}" readonly hidden>
                </div>
              </div>

              <h4>Password Reset</h4>
              <!-- item -->
              <div class="item">

              	<div class="icon">
              		<i class="fa fa-user-o"></i>
              	</div>

                  <input type="password" id="password" name="password"  name="password" placeholder="Enter password">
              </div>
              <!-- item -->

              <!-- item -->
              <div class="item">
              	<div class="icon">
              		<i class="fa fa-lock"></i>
              	</div>
                  <input type="password" id="password_confirmation" name="password_confirmation"  name="password_confirmation" placeholder="Enter password again">
              </div>
              <!-- item -->

              <!-- item -->
              
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
          
          <!---->
          <div class="clearfix"></div>
        </div>
      <!---->
    </div>
</div>

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
        $('#loginform').validate({
            rules: {
                password: {
                    required: true,
                },
                password_confirmation: {
                    required: true,
                },
            },
            messages: {
                password: {
                    required: "Please enter a password",
                },
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
                var form = $('#loginform');
                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                           // form.find('button[type="submit"]').button('loading');
                           $("#loadList").css('display','block');
                        },
                        success:function(data){
                          $("#loadList").css('display','none');
                            // form.find('button[type="submit"]').button('reset');
                            form[0].reset();
                           
                            notify(data.status, 'success');
                            window.location.href = "{{route('login')}}";
                            
                        },
                        error: function(errors) {
                          $("#loadList").css('display','none');
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });
    </script>
@endpush

