<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Responsable;
use App\Models\Oficina;
use App\Models\Cargo;
use App\Models\OficinaCargo;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ¡MAGIA! Forzamos a Filament Shield a regenerar todos los roles y permisos de tus recursos
        $this->command->info('Generando permisos de Filament Shield...');
        $this->command->call('shield:generate', ['--all' => true]);

        // 2. Creamos la Oficina por defecto
        $oficina = Oficina::firstOrCreate(
            ['descripcion' => 'SISTEMAS Y TECNOLOGÍA']
        );

        // 3. Creamos el Cargo por defecto
        $cargo = Cargo::firstOrCreate(
            ['descripcion' => 'ADMINISTRADOR DE SISTEMA']
        );

        // 4. Vinculamos la Oficina con el Cargo
        $oficinaCargo = OficinaCargo::firstOrCreate([
            'id_oficinas' => $oficina->idoficinas ?? $oficina->id, 
            'id_cargos' => $cargo->idcargos ?? $cargo->id
        ]);

        // 5. Creamos al Responsable asignándole su Oficina/Cargo
        $responsableAdmin = Responsable::firstOrCreate(
            ['ci' => '0000000'],
            [
                'nombre_apellido' => 'Administrador Principal',
                'numero_item' => '0',
                'id_oficinas_cargos' => $oficinaCargo->idoficinas_cargos ?? $oficinaCargo->id, 
            ]
        );

        // 6. Creamos la cuenta de usuario
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin'), 
                'responsable_id' => $responsableAdmin->idresponsables ?? $responsableAdmin->id,
            ]
        );

        // 7. Buscamos el rol 'super_admin' (que ya fue creado automáticamente por shield:generate)
        // y se lo asignamos al usuario
        $rolAdmin = Role::where('name', 'super_admin')->first();
        if ($rolAdmin) {
            $superAdmin->assignRole($rolAdmin);
        }

        $this->command->info('¡Base de datos y permisos sembrados con éxito! Usuario: admin@seduca.gob.bo');
    }
}