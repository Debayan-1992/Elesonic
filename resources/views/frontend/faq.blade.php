@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'FAQ')
 <h3 class="faq-heading">FAQ'S</h3>
        <section class="faq-container">
            @foreach($faqContent as $row)
            <div class="faq-one">
                <!-- faq question -->
                <h3 class="faq-page">{{ $row->question}}</h3>
                <!-- faq answer -->
                <div class="faq-body">
                {{ $row->answer}}
                </div>
            </div>
            @endforeach
        </section>
</div>
@endsection