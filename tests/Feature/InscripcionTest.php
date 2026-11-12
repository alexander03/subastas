<?php

namespace Tests\Feature;

use App\Mail\InscripcionPendienteMail;
use App\Mail\InscripcionRecibidaMail;
use App\Models\Inscripcion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

class InscripcionTest extends ProcesoSubastaTestCase
{
    private function payload($bien, $etapa, $usuario, array $extra = []): array
    {
        return array_merge([
            'fecha' => now()->toDateTimeString(), 'monto' => 100, 'situacion' => 'P',
            'facturacion' => 'boleta', 'etapa_id' => $etapa->id,
            'bien_id' => $bien->id, 'usuario_id' => $usuario->id,
        ], $extra);
    }

    private function dentroDeHorario()
    {
        return $this->crearProceso([now()->subDay(), now()->addDay()], [now()->addDays(2), now()->addDays(3)]);
    }

    private function voucher()
    {
        return UploadedFile::fake()->image('voucher.jpg');
    }

    public function test_inscripcion_valida_con_voucher_se_guarda_y_envia_correos()
    {
        Mail::fake();
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, ['image' => $this->voucher()]), ['Accept' => 'application/json'])
            ->assertOk();

        $this->assertDatabaseCount('inscripcions', 1);
        Mail::assertSent(InscripcionPendienteMail::class, fn ($m) => $m->hasTo('organizador@test.com'));
        Mail::assertSent(InscripcionRecibidaMail::class, fn ($m) => $m->hasTo('participante@test.com'));
    }

    public function test_los_correos_se_renderizan()
    {
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        $ins = Inscripcion::create($this->payload($bien, $etapa, $user));
        $ins->load(['usuario', 'bien.proceso.empresa', 'bien.producto']);

        $pendiente = (new InscripcionPendienteMail($ins))->render();
        $this->assertStringContainsString('Juan', $pendiente);
        $this->assertStringContainsString('Proceso test', $pendiente);
        $this->assertStringContainsString('Producto test', $pendiente);

        $recibida = (new InscripcionRecibidaMail($ins))->render();
        $this->assertStringContainsString('voucher de pago', $recibida);
    }

    public function test_sin_voucher_es_rechazada()
    {
        Mail::fake();
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->postJson('/api/inscripcion', $this->payload($bien, $etapa, $user))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Debe subir su voucher de pago. Sin este documento no se puede aprobar su inscripción.']);
        $this->assertDatabaseCount('inscripcions', 0);
        Mail::assertNothingSent();
    }

    public function test_inscripcion_con_periodo_cerrado_es_rechazada()
    {
        [$proceso, $bien, $etapa] = $this->crearProceso([now()->subDays(3), now()->subDay()], [now()->addDay(), now()->addDays(2)]);
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, ['image' => $this->voucher()]), ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'El periodo de inscripciones ha finalizado.']);
        $this->assertDatabaseCount('inscripcions', 0);
    }

    public function test_inscripcion_antes_de_iniciar_es_rechazada()
    {
        [$proceso, $bien, $etapa] = $this->crearProceso([now()->addDay(), now()->addDays(2)], [now()->addDays(3), now()->addDays(4)]);
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, ['image' => $this->voucher()]), ['Accept' => 'application/json'])
            ->assertStatus(422);
        $this->assertDatabaseCount('inscripcions', 0);
    }

    public function test_bien_inexistente_devuelve_404()
    {
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->postJson('/api/inscripcion', $this->payload($bien, $etapa, $user, ['bien_id' => 9999]))->assertStatus(404);
    }

    public function test_fallo_de_correo_no_rompe_la_inscripcion()
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp caido'));
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, ['image' => $this->voucher()]), ['Accept' => 'application/json'])
            ->assertOk();
        $this->assertDatabaseCount('inscripcions', 1);
    }

    public function test_empresa_sin_email_usa_fallback_y_no_falla()
    {
        Mail::fake();
        config(['mail.from.address' => null]);
        [$proceso, $bien, $etapa] = $this->crearProceso([now()->subDay(), now()->addDay()], [now()->addDays(2), now()->addDays(3)], 'P', false);
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, ['image' => $this->voucher()]), ['Accept' => 'application/json'])
            ->assertOk();
        Mail::assertNotSent(InscripcionPendienteMail::class);
        Mail::assertSent(InscripcionRecibidaMail::class);
    }

    public function test_no_se_puede_aprobar_sin_voucher()
    {
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);
        $ins = Inscripcion::create($this->payload($bien, $etapa, $user));

        $this->postJson("/api/actualizar-inscripcion/{$ins->id}", ['situacion' => 'A'])->assertStatus(422);
        $this->assertSame('P', $ins->fresh()->situacion);
    }

    public function test_rechazar_no_exige_voucher_y_aprobar_con_voucher_funciona()
    {
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);
        $ins = Inscripcion::create($this->payload($bien, $etapa, $user));

        $this->postJson("/api/actualizar-inscripcion/{$ins->id}", ['situacion' => 'R'])->assertOk();
        $this->post("/api/actualizar-inscripcion/{$ins->id}", ['situacion' => 'A', 'image' => $this->voucher()], ['Accept' => 'application/json'])
            ->assertOk();
        $this->assertSame('A', $ins->fresh()->situacion);

        // con el voucher ya guardado, aprobar de nuevo sin adjuntar funciona
        $this->postJson("/api/actualizar-inscripcion/{$ins->id}", ['situacion' => 'A'])->assertOk();
    }
    public function test_payload_minimo_se_completa_y_queda_pendiente()
    {
        Mail::fake();
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post("/api/inscripcion", [
            "bien_id" => $bien->id,
            "usuario_id" => $user->id,
            "monto" => 100,
            "situacion" => "A", // el participante no puede auto-aprobarse
            "image" => $this->voucher(),
        ], ["Accept" => "application/json"])->assertOk();

        $this->assertDatabaseHas("inscripcions", ["bien_id" => $bien->id, "situacion" => "P", "etapa_id" => $etapa->id]);
    }

    public function test_voucher_debe_ser_imagen_o_pdf()
    {
        Mail::fake();
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $this->post("/api/inscripcion", [
            "bien_id" => $bien->id,
            "usuario_id" => $user->id,
            "image" => UploadedFile::fake()->create("aa.txt", 0),
        ], ["Accept" => "application/json"])->assertStatus(422);

        $this->assertDatabaseCount("inscripcions", 0);
    }


    private function inscribirSinNotificar($notificar = false)
    {
        [$proceso, $bien, $etapa] = $this->dentroDeHorario();
        $user = $this->crearUsuario();
        Sanctum::actingAs($user);

        $respuesta = $this->post('/api/inscripcion', $this->payload($bien, $etapa, $user, [
            'image' => $this->voucher(),
            'notificar' => $notificar,
        ]), ['Accept' => 'application/json'])->assertOk();

        return $respuesta->json('id');
    }

    public function test_notificar_false_no_envia_correos_al_crear()
    {
        Mail::fake();
        $this->inscribirSinNotificar('false');

        $this->assertDatabaseCount('inscripcions', 1);
        Mail::assertNothingSent();
    }

    public function test_notificar_por_defecto_sigue_enviando_al_crear()
    {
        Mail::fake();
        $this->inscribirSinNotificar(true);

        Mail::assertSent(InscripcionPendienteMail::class, 1);
        Mail::assertSent(InscripcionRecibidaMail::class, 1);
    }

    public function test_endpoint_notificar_envia_solo_el_aviso_al_organizador()
    {
        Mail::fake();
        $id = $this->inscribirSinNotificar('false');

        $this->postJson("/api/inscripcion/{$id}/notificar")
            ->assertOk()
            ->assertJson(['message' => 'Notificación enviada.', 'destinatario' => 'organizador@test.com']);

        Mail::assertSent(InscripcionPendienteMail::class, fn ($m) => $m->hasTo('organizador@test.com'));
        Mail::assertNotSent(InscripcionRecibidaMail::class);
    }

    public function test_endpoint_notificar_rechaza_inscripcion_inexistente_o_ya_resuelta()
    {
        Mail::fake();
        $id = $this->inscribirSinNotificar('false');

        $this->postJson('/api/inscripcion/999999/notificar')->assertStatus(404);

        \App\Models\Inscripcion::find($id)->update(['situacion' => 'A']);
        $this->postJson("/api/inscripcion/{$id}/notificar")->assertStatus(422);
        Mail::assertNothingSent();
    }

    public function test_endpoint_notificar_exige_autenticacion()
    {
        $this->postJson('/api/inscripcion/1/notificar')->assertStatus(401);
    }
}
