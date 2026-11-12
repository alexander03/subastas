<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Empresa;
use App\Models\Etapa;
use App\Models\Producto;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Base para los tests de subasta. Usa SQLite en memoria (ver phpunit.xml),
 * nunca la base MySQL real.
 */
abstract class ProcesoSubastaTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        // Las tablas de ubigeo vienen de ubigeo.sql, no de migraciones
        foreach (['departments', 'provinces', 'districts'] as $t) {
            Schema::create("ubigeo_peru_$t", function ($table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->string('department_id')->nullable();
                $table->string('province_id')->nullable();
            });
        }
        // Disco falso: no escribe en public/media
        Storage::fake('media');
        config(['media-library.disk_name' => 'media']);
    }

    /**
     * Crea proceso + bien + etapas. $inscripcion y $subasta son [inicio, fin].
     */
    protected function crearProceso(array $inscripcion, array $subasta, string $situacionBien = 'P', bool $conEmpresa = true): array
    {
        // $conEmpresa=false => empresa existente pero sin correo (empresa_id es obligatorio)
        $empresa = Empresa::create([
            'ruc' => '20123456789', 'razonsocial' => 'Empresa Test', 'direccion' => 'X',
            'telefono' => '999', 'email' => $conEmpresa ? 'organizador@test.com' : '',
        ]);

        $proceso = Proceso::create([
            'nombre' => 'Proceso test', 'descripcion' => 'desc',
            'fechainicio' => now()->addHour(), 'fechafin' => now()->addDay(),
            'tipo' => 'Particular', 'situacion' => 'S', 'modalidad' => 'Electrónica',
            'bases' => 'N', 'tiempo' => 20, 'empresa_id' => optional($empresa)->id,
        ]);
        $producto = new Producto([
            'nombre' => 'Producto test', 'descripcion' => 'd', 'valor' => 1000,
            'situacion' => 'P', 'categoria_id' => 1, 'distrito_id' => '150101', 'moneda' => 'DOLARES',
        ]);
        $producto->portada = 'null';
        $producto->save();
        $bien = Bien::create([
            'situacion' => $situacionBien, 'valorreferencia' => 1000, 'proceso_id' => $proceso->id,
            'producto_id' => $producto->id, 'garantia' => 0.1,
        ]);
        $e1 = Etapa::create(['tipo' => '1', 'fechainicio' => $inscripcion[0], 'fechafin' => $inscripcion[1], 'situacion' => 'P', 'proceso_id' => $proceso->id]);
        Etapa::create(['tipo' => '3', 'fechainicio' => $subasta[0], 'fechafin' => $subasta[1], 'situacion' => 'P', 'proceso_id' => $proceso->id]);

        return [$proceso->fresh(), $bien, $e1];
    }

    protected function crearUsuario(string $email = 'participante@test.com'): User
    {
        return User::create([
            'nombres' => 'Juan', 'apellidopaterno' => 'Perez', 'apellidomaterno' => 'Diaz',
            'dni' => '12345678', 'telefono' => '999', 'direccion' => 'x', 'distrito_id' => '150101',
            'fechanacimiento' => '1990-01-01', 'email' => $email, 'password' => 'secret123', 'tipousuario_id' => 2,
        ]);
    }
}
