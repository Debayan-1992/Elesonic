@component('mail::message')

# Hi {{$name}},<br>
<hr>
Your service has been approved for {{$price}}.<br>
<br>
The following message has been given by the admin:<br>
{{$message}}<br>
<br>
Please click the below button to go to the payment link.<br>
Please also note that the payment link shall be non-functional after 2 hours from now.<br> 
<hr>
@component('mail::button', ['url' => $payment_link])
Pay
@endcomponent

Thanks,<br>
# {{ config('app.name') }}
@endcomponent