<?php error_reporting(0) ?>
@section('pageheader', 'Reports')
@extends('layouts.app')
<style>
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>
@section('content')
    <section class="content-header">
        <h1>
            Report Management
            
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Order Reports</h3>

                <div class="box-tools pull-right">
                    {{-- Tools --}}
                </div>
            </div>
           
            <div class="box-body">
            <form action="{{route('dashboard.reports.generate-excel')}}" method="post">
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
          </section>
         
@endsection

@push('script')

 
@endpush
