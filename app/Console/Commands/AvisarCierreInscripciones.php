<?php

namespace App\Console\Commands;

use App\Mail\InscripcionPendienteMail;
use App\Mail\VoucherPendienteMail;
use App\Models\Etapa;
use App\Models\Inscripcion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Avisa de las inscripciones pendientes cuando la etapa de inscripción (tipo 2)
 * de un proceso cierra en N días (por defecto 3).
 *
 *  - Pendiente SIN voucher: recordatorio al participante.
 *  - Pendiente CON voucher: aviso a la empresa organizadora para que apruebe.
 */
class AvisarCierreInscripciones extends Command
{
    protected $signature = 'inscripciones:avisar-cierre
                            {--dias=3 : Días que faltan para que cierre la inscripción}
                            {--dry-run : Solo lista lo que enviaría, sin enviar correos}';

    protected $description = 'Notifica las inscripciones pendientes de los procesos cuya etapa de inscripción cierra en N días';

    public function handle()
    {
        $dias = (int) $this->option('dias');
        $dryRun = (bool) $this->option('dry-run');
        $objetivo = now()->addDays($dias)->toDateString();

        $procesoIds = Etapa::where('tipo', '2')
            ->whereDate('fechafin', $objetivo)
            ->pluck('proceso_id')
            ->unique();

        $this->info("Inscripción que cierra el {$objetivo}: {$procesoIds->count()} proceso(s).");

        $enviados = 0;
        $fallidos = 0;

        $inscripciones = Inscripcion::where('situacion', 'P')
            ->whereHas('bien', function ($q) use ($procesoIds) {
                $q->whereIn('proceso_id', $procesoIds);
            })
            ->with(['usuario', 'bien.proceso.empresa', 'bien.producto'])
            ->get();

        foreach ($inscripciones as $inscripcion) {
            $sinVoucher = $inscripcion->getMedia('images')->isEmpty();

            if ($sinVoucher) {
                $destino = optional($inscripcion->usuario)->email;
                $mailable = new VoucherPendienteMail($inscripcion);
            } else {
                $empresa = optional(optional($inscripcion->bien)->proceso)->empresa;
                $destino = optional($empresa)->email ?: config('mail.from.address');
                $mailable = new InscripcionPendienteMail($inscripcion);
            }

            $tipo = $sinVoucher ? 'recordatorio_voucher' : 'pendiente_aprobacion';

            if (empty($destino)) {
                $this->warn("Inscripción {$inscripcion->id}: sin correo de destino ({$tipo}).");
                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] Inscripción {$inscripcion->id} → {$destino} ({$tipo})");
                continue;
            }

            try {
                Mail::to($destino)->send($mailable);
                $enviados++;
                $this->line("Inscripción {$inscripcion->id} → {$destino} ({$tipo})");
            } catch (\Throwable $e) {
                $fallidos++;
                Log::warning("Aviso de cierre: no se pudo enviar la inscripción {$inscripcion->id}: " . $e->getMessage());
                $this->error("Inscripción {$inscripcion->id}: {$e->getMessage()}");
            }
        }

        $this->info("Pendientes: {$inscripciones->count()} | enviados: {$enviados} | fallidos: {$fallidos}");

        return Command::SUCCESS;
    }
}
