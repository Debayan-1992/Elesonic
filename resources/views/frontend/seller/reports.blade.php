@extends('layouts.frontend.app')
@section('pageheader', 'Reports')
@section('content')
<div class="container">
    <div class="main-body">
        <div class="row">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @include('layouts.frontend.leftpanel')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('seller.generate-excel')}}" method="post" id="pass_form">
                      
                            @csrf
                           
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Start Date</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="date" required="" name="startDate" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">End Date</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="date" required="" name="endDate" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Generate Order Report">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                 <div class="card">
                    <div class="card-body">
                        <form action="{{route('seller.generate-excel-revenue')}}" method="post" id="pass_form">
                      
                            @csrf
                           
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Start Date</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="date" required="" name="startDate" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">End Date</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="date" required="" name="endDate" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Generate Revenue Report">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </dIv>
        </dIv>
    </dIv>
</dIv>
@endsection