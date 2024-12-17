@component('mail::message')
Forgot

Hi, {{$user->first_name . ' ' . $user->last_name}}
<P>
We have received your request to reset your account password

You can use the following otp code to recover your account: <b>{{$otp}}</b><br>
Thanks! <br>
{{ config('app.name') }}
@endcomponent