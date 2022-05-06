@component('mail::message')

# Hi {{$name}},<br>
<hr>
{{$text}}<br>

<hr>


Thanks,<br>
# {{ config('app.name') }}
@endcomponent