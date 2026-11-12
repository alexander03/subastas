@component('mail::message')
# Restablezca su contraseña.

<p class="lead">Ingrese este código en la plataforma para crear una nueva contraseña.</p>

@component('mail::codigo', ['etiqueta' => 'Su código de verificación'])
{{ $code }}
@endcomponent

<p class="sub">El código vence en una hora. Si usted no solicitó este cambio, ignore este mensaje: su contraseña no cambiará.</p>
@endcomponent
