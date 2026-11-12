@component('mail::message')
<h1>Hemos recibido su solicitud para restablecer la contraseña de su cuenta</h1>
<p>Puede utilizar el siguiente código para recuperar su cuenta</p>

@component('mail::panel')
{{ $code }}
@endcomponent

<p>La duración permitida del código es de una hora desde el envío del mensaje</p>
@endcomponent