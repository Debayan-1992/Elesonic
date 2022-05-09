
@extends('layouts.frontend.app')
@section('content')
@section('pageheader', $cmsContent->page_title)
    

    <!-- inner banner end -->


    <section class="about-us inner-sec-pad">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-12">

                    <h3>{{$cmsContent->page_name}}</h3>
                    {!!$cmsContent->content!!}
                </div>

            </div>
        </div>
    </section>

    <!-- client testimonial start -->
    <!-- client testimonial start -->
   

    
    <!-- newsletter end -->
    <!-- footer start -->
    @endsection


