@component('mail::message')
# Recibimos su inscripción.

<p class="lead">Hola {{ optional($inscripcion->usuario)->nombres }}, su inscripción está pendiente de aprobación.</p>

@component('mail::datos', ['filas' => [
'Proceso' => optional(optional($inscripcion->bien)->proceso)->nombre,
'Bien' => optional(optional($inscripcion->bien)->producto)->nombre,
'Estado' => 'Pendiente de aprobación',
]])
@endcomponent

@component('mail::aviso', ['titulo' => 'El voucher de pago es obligatorio.'])
Revisaremos el voucher que adjuntó. Sin este documento no es posible aprobar su inscripción.
@endcomponent

@component('mail::button', ['url' => config('app.front_url')])
Ir a Subasta Perú
@endcomponent

<p class="sub">Puede consultar el estado de su inscripción desde su cuenta.</p>
@endcomponent
