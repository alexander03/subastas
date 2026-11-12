<table class="datos" width="100%" cellpadding="0" cellspacing="0" role="presentation">
@foreach ($filas as $etiqueta => $valor)
<tr>
<td class="dato-etiqueta{{ $loop->last ? ' dato-ultima' : '' }}">{{ $etiqueta }}</td>
<td class="dato-valor{{ $loop->last ? ' dato-ultima' : '' }}">{{ $valor }}</td>
</tr>
@endforeach
</table>
