@component('mail::message')
# Falta su voucher de pago.

<p class="lead">Hola {{ optional($inscripcion->usuario)->nombres }}, aún no recibimos el voucher de su inscripción.</p>

@component('mail::datos', ['filas' => [
'Proceso' => optional(optional($inscripcion->bien)->proceso)->nombre,
'Bien' => optional(optional($inscripcion->bien)->producto)->nombre,
'Estado' => 'Pendiente de voucher',
]])
@endcomponent

@component('mail::aviso', ['titulo' => 'El voucher de pago es obligatorio.'])
Sin este documento no es posible aprobar su inscripción. Súbalo desde su cuenta lo antes posible.
@endcomponent

@component('mail::button', ['url' => config('app.front_url')])
Ir a Subasta Perú
@endcomponent

<p class="sub">Si ya realizó el pago, solo falta que adjunte el comprobante.</p>
@endcomponent
