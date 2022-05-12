@component('mail::message')

# Hi {{$name}},<br>
<hr>
{!! $text !!}

<hr>


Thanks,<br>
# {{ config('app.name') }}
@endcomponent