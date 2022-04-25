@extends('layouts.app')

@section('content')

    <section class="content-header">
        <h1>
            Dashboard
            <small>Control Panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Title</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                Start creating your amazing application!
            </div>
            <div class="box-footer">
                Footer
            </div>
        </div>
    </section>

@endsection
