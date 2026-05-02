<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4 text-center sm:text-left">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Bienvenido, {{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()->name }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400">
                Desde aquí puede gestionar los activos fijos que tiene bajo su custodia en el SEDUCA. Seleccione una acción rápida:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                
                <!-- BOTÓN 1: CONSULTA -->
                <x-filament::button 
                    tag="a" 
                    href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" 
                    icon="heroicon-o-magnifying-glass" 
                    color="info" 
                    size="xl"
                    class="w-full justify-center"
                >
                    Consultar Mis Activos Asignados
                </x-filament::button>

                <!-- BOTÓN 2: TRANSFERENCIA -->
                <x-filament::button 
                    tag="a" 
                    href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" 
                    icon="heroicon-o-arrows-right-left" 
                    color="warning" 
                    size="xl"
                    class="w-full justify-center"
                >
                    Realizar Transferencia a otro Funcionario
                </x-filament::button>

            </div>
            
            <p class="text-xs text-gray-400 mt-2">
                * Nota: Para transferir un activo, ingrese a la lista, seleccione las casillas de los bienes y presione "Transferir Bienes".
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>