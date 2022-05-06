@component('mail::message')

# Hi {{$name}},<br>
<hr>
Below given is the form link to reset your Elesonic account's password<br>

<hr>
@component('mail::button', ['url' => $link])
Set Password
@endcomponent

Thanks,<br>
# {{ config('app.name') }}
@endcomponent