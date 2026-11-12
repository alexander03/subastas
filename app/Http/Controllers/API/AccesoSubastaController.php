<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use Illuminate\Http\Request;

class AccesoSubastaController extends Controller
{
    /**
     * Indica si un usuario aún puede ingresar a la sala de subasta de un bien.
     * No se permite el ingreso una vez iniciada la subasta (estado distinto de
     * "P" o hora de inicio vencida).
     */
    public function verificar(Request $request)
    {
        $data = $request->validate([
            'bien_id' => 'required|integer',
        ]);

        $bien = Bien::find($data['bien_id']);
        if (is_null($bien)) {
            return response()->json(['puede_ingresar' => false, 'motivo' => 'Bien no encontrado.'], 404);
        }

        if ($bien->situacion !== 'P') {
            return response()->json([
                'puede_ingresar' => false,
                'motivo' => $bien->situacion === 'F'
                    ? 'La subasta ya finalizó.'
                    : 'La subasta ya inició. No se permite el ingreso de nuevos participantes.',
            ]);
        }

        $inicio = $bien->proceso->inicioSubasta();
        if ($inicio && now()->gte($inicio)) {
            return response()->json([
                'puede_ingresar' => false,
                'motivo' => 'La hora de inicio de la subasta ya pasó. No se permite el ingreso.',
            ]);
        }

        return response()->json(['puede_ingresar' => true, 'motivo' => null]);
    }

    /**
     * Instrucciones de uso de la plataforma (apartado de ayuda).
     */
    public function instrucciones()
    {
        return response()->json([
            'manual_url' => url('manual-usuario'),
            'pasos' => [
                ['titulo' => 'Crear una cuenta', 'detalle' => 'Regístrese con sus datos. La cuenta creada puede participar en todas las etapas del proceso.'],
                ['titulo' => 'Inscribirse', 'detalle' => 'Elija el bien de su interés e inscríbase dentro del horario de inscripciones.'],
                ['titulo' => 'Subir voucher de pago', 'detalle' => 'Es obligatorio subir su voucher de pago; sin él no se puede aprobar su inscripción.'],
                ['titulo' => 'Esperar la aprobación', 'detalle' => 'El organizador revisará su inscripción y la aprobará o rechazará.'],
                ['titulo' => 'Ingresar a la subasta', 'detalle' => 'Ingrese antes de la hora de inicio; después de iniciada ya no se permite el ingreso.'],
                ['titulo' => 'Pujar', 'detalle' => 'Realice sus pujas durante la subasta. Al finalizar se muestra el ganador.'],
            ],
        ]);
    }
}
