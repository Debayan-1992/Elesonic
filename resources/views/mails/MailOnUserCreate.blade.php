@component('mail::message')

# Hi {{$name}},<br>
<hr>
Below given are your credentials to login to Elesonic<br>

Eamil: {{$email}}<br>
Password: {{$password}}<br>

Please also click the below button to verify your account.
<hr>
@component('mail::button', ['url' => $verify_url])
Verify Email
@endcomponent

Thanks,<br>
# {{ config('app.name') }}
@endcomponent