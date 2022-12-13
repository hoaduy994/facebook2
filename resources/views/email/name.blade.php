@component('mail::message')
# New generation

The body of your message.<br>
Welcome {{$user->name}}<br>
Your OTP is <strong style="color:'blue'">{{$OTP}}</strong>  will be expired for 60 seconds

Thanks,<br>
<strong style="color:'red'">OK</strong>
@endcomponent