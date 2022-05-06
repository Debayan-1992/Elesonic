<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="css/style.css">

    <link rel="icon" href="Favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <title>{{env('APP_NAME')}}</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <img src="{{ asset('custom_resource/images/logo.png') }}" style="width:50px; height:50px;">
        <a class="navbar-brand" href="#">{{env('APP_NAME')}}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{--<div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Register</a>
                </li>
            </ul>

        </div>--}}
    </div>
</nav>

<main class="login-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Password Reset</div>
                    <div class="card-body">
                        <form action="{{route('resetPassword')}}" method="post" id="pass_form">
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
                            
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input type="text" id="password" name="password"class="form-control" name="password" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Re-enter Password</label>
                                <div class="col-md-6">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
        var val = $("#password").val();
		$("#pass_form").validate({
			rules:{
				password: {
					minlength: 6,
					maxlength: 30,
					required: true,
					//checklower: true,
					//checkupper: true,
					//checkdigit: true,
				},
				password_confirmation: {
					equalTo: "#password",
				},
			},
			messages: {
				password: {
				//pwcheck: "Password is not strong enough",
				//checklower: "Need atleast 1 lowercase alphabet",
				//checkupper: "Need atleast 1 uppercase alphabet",
				//checkdigit: "Need atleast 1 digit",
				},
			},
			
		});
	})
</script>





</body>
</html>