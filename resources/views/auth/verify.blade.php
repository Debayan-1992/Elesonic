@extends('layouts.app')

@section('content')

<section class="content-header">
    <h1>
        Dashboard
        <small>Verify Email Address</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Verify Email Address</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-widget">
                    <div class="box-header with-border">
                        <div class="user-block">
                            <img class="img-circle" src="{{Auth::user()->avatar}}" alt="">
                            <span class="username"><a href="#">{{Auth::user()->name}}</a></span>
                            <span class="description">Joined on - {{Auth::user()->created_at}}</span>
                        </div>

                        <div class="box-tools">
                        </div>
                    </div>

                    <div class="box-body">
                        <p>Before proceeding, please check your email for a verification link.</p>

                        <p>If you did not receive the email. click on the Resend button to recieve the mail again.</p>
                    </div>

                    <div class="box-footer text-right">
                        <a class="btn btn-primary" href="{{ route('verification.resend') }}">Click here to request another</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
