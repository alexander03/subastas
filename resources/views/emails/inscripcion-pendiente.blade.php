@component('mail::message')
# Hay un participante por aprobar.

<p class="lead">Se registró una nueva inscripción que requiere su revisión.</p>

@component('mail::datos', ['filas' => [
'Participante' => trim(optional($inscripcion->usuario)->nombres.' '.optional($inscripcion->usuario)->apellidopaterno.' '.optional($inscripcion->usuario)->apellidomaterno),
'DNI' => optional($inscripcion->usuario)->dni,
'Correo' => optional($inscripcion->usuario)->email,
'Proceso' => optional(optional($inscripcion->bien)->proceso)->nombre,
'Bien' => optional(optional($inscripcion->bien)->producto)->nombre,
'Fecha' => optional($inscripcion->fecha) ? \Carbon\Carbon::parse($inscripcion->fecha)->format('d/m/Y H:i') : '',
]])
@endcomponent

@component('mail::aviso', ['titulo' => 'Revise el voucher de pago.'])
Sin este documento la inscripción no puede aprobarse.
@endcomponent

@component('mail::button', ['url' => config('app.front_url')])
Revisar inscripción
@endcomponent
@endcomponent
