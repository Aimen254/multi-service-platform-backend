@component('mail::message')
Verify Account

Hi, {{$user->first_name . ' ' . $user->last_name}}
<P>
We just need to verify your email address before you can access {{ config('app.name') }}.

Verify your email address by using otp code: <b>{{$otp}}</b><br>
Thanks! <br>
{{ config('app.name') }}
@endcomponent