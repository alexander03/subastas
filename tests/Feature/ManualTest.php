<?php

namespace Tests\Feature;

use Tests\TestCase;

class ManualTest extends TestCase
{
    public function test_pagina_manual_es_publica()
    {
        $this->get('/manual-usuario')->assertOk()->assertSee('Manual de usuario')->assertSee('voucher');
    }

    public function test_endpoint_instrucciones_devuelve_pasos_y_url()
    {
        $r = $this->getJson('/api/instrucciones')->assertOk();
        $r->assertJsonStructure(['manual_url', 'pasos' => [['titulo', 'detalle']]]);
        $this->assertCount(6, $r->json('pasos'));
        $this->assertStringEndsWith('/manual-usuario', $r->json('manual_url'));
    }
}
