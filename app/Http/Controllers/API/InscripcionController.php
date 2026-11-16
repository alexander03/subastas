<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inscripcion;
use App\Http\Resources\InscripcionResource;
use App\Models\Bases;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscripcionPendienteMail;
use App\Mail\InscripcionRecibidaMail;
use App\Mail\VoucherPendienteMail;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'bien_id' => 'nullable|integer',
            'etapa_id' => 'nullable|integer',
        ]);
        $list = Inscripcion::orderBy('fecha', 'asc');
        if (!is_null($request->input('bien_id')) && !empty($request->input('bien_id'))) {
            $list = $list->where('bien_id', '=', $request->input('bien_id'));
        }
        if (!is_null($request->input('etapa_id')) && !empty($request->input('etapa_id'))) {
            $list = $list->where('etapa_id', '=', $request->input('etapa_id'));
        }

        $list = $list->latest()->paginate();
        $lista = InscripcionResource::collection($list);
        return response()->json($lista);
    }

    public function inscripcionesByUser(Request $request)
    {
        $request->validate([
            'usuario_id' => 'nullable|integer',
        ]);
        $list = Inscripcion::orderBy('fecha', 'asc');
        if (!is_null($request->input('usuario_id')) && !empty($request->input('usuario_id'))) {
            $list = $list->where('usuario_id', '=', $request->input('usuario_id'));
        }

        $list = $list->get();
        $lista = InscripcionResource::collection($list);
        return response()->json($lista);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->normalizarNotificar($request);

        $request->validate([
            // Si es false no se envía ningún correo al crear (se puede enviar luego con /inscripcion/{id}/notificar). Por defecto: true.
            'notificar' => 'nullable|boolean',
            'bien_id' => 'required|integer',
            'usuario_id' => 'required|integer',
            'etapa_id' => 'nullable|integer',
            'fecha' => 'nullable|date',
            'monto' => 'nullable|numeric',
            'facturacion' => 'nullable',
            'comentario' => 'nullable|string',
            'situacion' => 'nullable|string|in:P,A,R',
            // Voucher de pago: obligatorio (se valida abajo con un mensaje propio)
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $bienInscrito = Bien::find($request->bien_id);
        if (is_null($bienInscrito)) {
            return response()->json(['message' => 'El bien indicado no existe.'], 404);
        }

        // Inscripciones solo dentro del horario de la etapa de inscripción
        $estado = $bienInscrito->proceso->estadoInscripcion();
        if ($estado === 'cerrada') {
            return response()->json(['message' => 'El periodo de inscripciones ha finalizado.'], 422);
        }
        if ($estado === 'no_iniciada') {
            return response()->json(['message' => 'El periodo de inscripciones aún no ha iniciado.'], 422);
        }

        // El voucher de pago es obligatorio para poder aprobar la inscripción
        if (!$request->hasFile('image')) {
            return response()->json(['message' => 'Debe subir su voucher de pago. Sin este documento no se puede aprobar su inscripción.'], 422);
        }

        // Datos que la BD exige y el participante no debe decidir:
        // toda inscripción nueva nace pendiente (solo el organizador la aprueba).
        $etapaInscripcion = $bienInscrito->proceso->etapaPorTipo('2');
        $datos = $request->except(['image', 'situacion', 'notificar']);
        $datos['situacion'] = 'P';
        $datos['fecha'] = $request->input('fecha') ?: now()->toDateString();
        $datos['facturacion'] = $request->input('facturacion') ?: '';
        $datos['etapa_id'] = $request->input('etapa_id') ?: optional($etapaInscripcion)->id;

        $obj = "";
        $error = DB::transaction(function () use ($request, &$obj, $datos) {
            $obj = Inscripcion::create($datos);
            if (isset($request['image'])) {
                $obj->clearMediaCollection('images');
                $obj->addMediaFromRequest('image')->toMediaCollection('images');
                $obj->save();
            }
            $bien = Bien::find($request->bien_id);
            if ($bien->proceso->bases == "S") {
                $base = array(
                    "proceso_id" => $bien->proceso_id,
                    "situacion" => 'P',
                    "comentario" => '',
                    "usuario_id" => $request->usuario_id
                );
                Bases::create($base);
            }

            $obj->load(['media']);
        });

        if ($this->quiereNotificar($request)) {
            $this->enviarNotificaciones($obj);
        }

        return response()->json($obj);
    }

    /**
     * "notificar" llega como texto en multipart ("true"/"false"/"1"/"0"): se normaliza a booleano.
     * Un valor inválido se deja tal cual para que la validación lo rechace.
     */
    private function normalizarNotificar(Request $request)
    {
        if (!is_null($request->input('notificar'))) {
            $valor = filter_var($request->input('notificar'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_null($valor)) {
                $request->merge(['notificar' => $valor]);
            }
        }
    }

    private function quiereNotificar(Request $request)
    {
        return $request->boolean('notificar', true);
    }

    /**
     * Avisa a la empresa organizadora que hay un participante por aprobar.
     * Un fallo de correo no debe romper la operación principal.
     */
    private function avisarAlOrganizador(Inscripcion $inscripcion)
    {
        try {
            $inscripcion->load(['usuario', 'bien.proceso.empresa', 'bien.producto']);
            $empresa = optional(optional($inscripcion->bien)->proceso)->empresa;
            $destino = optional($empresa)->email ?: config('mail.from.address');
            if (!empty($destino)) {
                Mail::to($destino)->send(new InscripcionPendienteMail($inscripcion));
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo avisar al organizador del voucher: ' . $e->getMessage());
        }
    }

    /**
     * Envía (o reenvía) al organizador el aviso de que hay un participante pendiente de aprobación.
     * Solo aplica a inscripciones pendientes. No envía nada al participante.
     */
    public function notificar($id)
    {
        $inscripcion = Inscripcion::find($id);
        if (is_null($inscripcion)) {
            return response()->json(['message' => 'La inscripción indicada no existe.'], 404);
        }

        if ($inscripcion->situacion !== 'P') {
            return response()->json(['message' => 'Solo se puede notificar una inscripción pendiente de aprobación.'], 422);
        }

        $inscripcion->load(['usuario', 'bien.proceso.empresa', 'bien.producto']);

        // Sin voucher no hay nada que aprobar: se le recuerda al participante que lo suba.
        // Con voucher, se avisa al organizador que hay un participante por aprobar.
        $sinVoucher = $inscripcion->getMedia('images')->isEmpty();
        if ($sinVoucher) {
            $destino = optional($inscripcion->usuario)->email;
            $tipo = 'recordatorio_voucher';
            $mailable = new VoucherPendienteMail($inscripcion);
            $sinDestino = 'No hay un correo de destino: el participante no tiene email.';
        } else {
            $empresa = optional(optional($inscripcion->bien)->proceso)->empresa;
            $destino = optional($empresa)->email ?: config('mail.from.address');
            $tipo = 'pendiente_aprobacion';
            $mailable = new InscripcionPendienteMail($inscripcion);
            $sinDestino = 'No hay un correo de destino: la empresa del proceso no tiene email.';
        }
        if (empty($destino)) {
            return response()->json(['message' => $sinDestino], 422);
        }

        try {
            Mail::to($destino)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar la notificación de inscripción pendiente: ' . $e->getMessage());
            return response()->json([
                'message' => 'No se pudo enviar el correo. Intente nuevamente.',
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }

        return response()->json(['message' => 'Notificación enviada.', 'tipo' => $tipo, 'destinatario' => $destino]);
    }

    /**
     * Avisa al organizador (participante pendiente) y confirma al participante
     * recordándole el voucher. Un fallo de correo no debe romper la inscripción.
     */
    private function enviarNotificaciones(Inscripcion $inscripcion)
    {
        try {
            $inscripcion->load(['usuario', 'bien.proceso.empresa', 'bien.producto']);
            $empresa = optional(optional($inscripcion->bien)->proceso)->empresa;
            $destinoAdmin = optional($empresa)->email ?: config('mail.from.address');
            if (!empty($destinoAdmin)) {
                Mail::to($destinoAdmin)->send(new InscripcionPendienteMail($inscripcion));
            }
            $correo = optional($inscripcion->usuario)->email;
            if (!empty($correo)) {
                Mail::to($correo)->send(new InscripcionRecibidaMail($inscripcion));
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de inscripción: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Inscripcion::find($id);
        return new InscripcionResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar($id, Request $request)
    {
        $this->normalizarNotificar($request);

        $request->validate([
            'situacion' => 'nullable|string|in:P,A,R',
            'comentario' => 'nullable|string',
            'monto' => 'nullable|numeric',
            'facturacion' => 'nullable',
            'fecha' => 'nullable|date',
            'etapa_id' => 'nullable|integer',
            'bien_id' => 'nullable|integer',
            'usuario_id' => 'nullable|integer',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            // Si es false, al subir el voucher no se avisa al organizador. Por defecto: true.
            'notificar' => 'nullable|boolean',
        ]);

        $obj = Inscripcion::find($id);
        $teniaVoucher = $obj->getMedia('images')->isNotEmpty();

        // No se puede aprobar una inscripción sin voucher de pago
        if (
            $request->input('situacion') === 'A'
            && !$request->hasFile('image')
            && $obj->getMedia('images')->isEmpty()
        ) {
            return response()->json(['message' => 'No se puede aprobar la inscripción: el participante no ha subido su voucher de pago.'], 422);
        }

        $obj->update($request->all());

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
            $obj->save();

            // Primer voucher de una inscripción pendiente: se avisa al organizador que ya hay algo que revisar.
            if (!$teniaVoucher && $obj->situacion === 'P' && $this->quiereNotificar($request)) {
                $this->avisarAlOrganizador($obj);
            }
        }

        $obj->load(['media']);
        $obj = new InscripcionResource($obj);


        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Inscripcion::find($id);
        if (!empty($obj)) {
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
