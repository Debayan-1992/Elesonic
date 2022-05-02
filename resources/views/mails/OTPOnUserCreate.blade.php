@component('mail::message')

# Hi {{$name}},<br>
<hr>
Below given is the OTP verification code from Elesonic<br>

<hr>
{{$otp}}

Thanks,<br>
# {{ config('app.name') }}
@endcomponent