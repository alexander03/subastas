<?php

namespace Tests\Feature;

class AccesoSubastaTest extends ProcesoSubastaTestCase
{
    private function verificar($bien)
    {
        return $this->getJson('/api/subasta/verificarAcceso?bien_id=' . $bien->id);
    }

    public function test_permite_ingreso_antes_del_inicio()
    {
        [, $bien] = $this->crearProceso([now()->subDays(2), now()->subDay()], [now()->addHour(), now()->addHours(2)]);
        $this->verificar($bien)->assertOk()->assertJson(['puede_ingresar' => true]);
    }

    public function test_bloquea_si_la_hora_de_inicio_ya_paso_aunque_el_bien_siga_pendiente()
    {
        [, $bien] = $this->crearProceso([now()->subDays(2), now()->subDay()], [now()->subMinute(), now()->addHour()], 'P');
        $this->verificar($bien)->assertOk()->assertJson(['puede_ingresar' => false]);
    }

    public function test_bloquea_si_la_subasta_esta_en_curso()
    {
        [, $bien] = $this->crearProceso([now()->subDays(2), now()->subDay()], [now()->addHour(), now()->addHours(2)], 'E');
        $this->verificar($bien)->assertJson(['puede_ingresar' => false])
            ->assertJsonFragment(['motivo' => 'La subasta ya inició. No se permite el ingreso de nuevos participantes.']);
    }

    public function test_bloquea_si_la_subasta_finalizo()
    {
        [, $bien] = $this->crearProceso([now()->subDays(2), now()->subDay()], [now()->subHours(2), now()->subHour()], 'F');
        $this->verificar($bien)->assertJson(['puede_ingresar' => false])
            ->assertJsonFragment(['motivo' => 'La subasta ya finalizó.']);
    }

    public function test_bien_inexistente_devuelve_404_y_sin_bien_id_422()
    {
        $this->getJson('/api/subasta/verificarAcceso?bien_id=9999')->assertStatus(404);
        $this->getJson('/api/subasta/verificarAcceso')->assertStatus(422);
    }

    public function test_sin_etapa_3_usa_fechainicio_del_proceso()
    {
        [$proceso, $bien] = $this->crearProceso([now()->subDays(2), now()->subDay()], [now()->addHour(), now()->addHours(2)]);
        $proceso->etapas()->where('tipo', '3')->delete();
        $proceso->update(['fechainicio' => now()->subMinute()]);
        $this->verificar($bien)->assertJson(['puede_ingresar' => false]);
    }
}
