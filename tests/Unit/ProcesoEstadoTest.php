<?php

namespace Tests\Unit;

use App\Models\Etapa;
use App\Models\Proceso;
use Tests\TestCase;

class ProcesoEstadoTest extends TestCase
{
    private function proceso(array $etapas, $fechainicio = null): Proceso
    {
        $p = new Proceso(['fechainicio' => $fechainicio]);
        $p->setRelation('etapas', collect(array_map(fn ($e) => new Etapa($e), $etapas)));
        return $p;
    }

    public function test_sin_etapa_de_inscripcion_no_restringe()
    {
        $this->assertSame('abierta', $this->proceso([])->estadoInscripcion());
    }

    public function test_estados_de_inscripcion()
    {
        $f = fn ($s) => date('Y-m-d H:i:s', time() + $s);
        $mk = fn ($a, $b) => $this->proceso([['tipo' => '1', 'fechainicio' => $a, 'fechafin' => $b]]);
        $this->assertSame('abierta', $mk($f(-60), $f(60))->estadoInscripcion());
        $this->assertSame('cerrada', $mk($f(-120), $f(-60))->estadoInscripcion());
        $this->assertSame('no_iniciada', $mk($f(60), $f(120))->estadoInscripcion());
    }

    public function test_inicio_subasta_prefiere_etapa_3_y_cae_a_fechainicio()
    {
        $con = $this->proceso([['tipo' => '3', 'fechainicio' => '2030-01-01 10:00:00', 'fechafin' => '2030-01-01 11:00:00']], '2029-01-01 00:00:00');
        $this->assertSame('2030-01-01 10:00:00', $con->inicioSubasta()->format('Y-m-d H:i:s'));
        $sin = $this->proceso([], '2029-01-01 00:00:00');
        $this->assertSame('2029-01-01 00:00:00', $sin->inicioSubasta()->format('Y-m-d H:i:s'));
        $this->assertNull($this->proceso([])->inicioSubasta());
    }
}
