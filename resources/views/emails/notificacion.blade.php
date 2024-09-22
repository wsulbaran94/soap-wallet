<x-mail::message>
# The subject of the email.
{{ $subject }}




# token de verificación
<div style='margin: 0px auto; margin-top: 20px'>
  <h2 style='text-aling: center; letter-spacing: 0.25em'>{{ $token }}</h2>
</div>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
