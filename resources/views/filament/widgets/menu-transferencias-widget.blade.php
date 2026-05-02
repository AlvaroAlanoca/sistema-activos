<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2 text-primary-600">
                <x-heroicon-o-briefcase class="w-6 h-6" />
                <span>ACTIVA - ACTIVOS FIJOS</span>
            </div>
        </x-slot>

        <div class="flex flex-col md:flex-row items-start gap-6 p-2">
            <!-- Icono principal de Transferencias -->
            <div class="p-4 bg-primary-500/10 rounded-xl shadow-sm border border-primary-500/20">
            <x-heroicon-o-cube class="w-12 h-12 text-primary-600" />
            </div>

            <!-- Contenedor de Enlaces -->
            <div class="flex-1 w-full">

                 <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Control de Bienes</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Enlace 1: Crear (Lleva al formulario en blanco) -->
                    <a href="{{ \App\Filament\Resources\ActaResource::getUrl('create') }}" 
                       class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-700 dark:text-gray-300 hover:text-primary-600">
                        <x-heroicon-o-plus-circle class="w-5 h-5 text-primary-500" />
                        <span class="font-medium">Entrega de Bienes</span>
                    </a>

                    <!-- Enlace 2: Lleva a Bienes -->
                    <a href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" 
                       class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-700 dark:text-gray-300 hover:text-primary-600">
                                               <x-heroicon-o-magnifying-glass class="w-5 h-5 text-primary-500" /> 

                        <span class="font-medium">Ver Disponibilidad</span>
                    </a>

                    <!-- Enlace 3: Imprimir (Lleva al historial) -->
                    <a href="{{ \App\Filament\Resources\ActaResource::getUrl('index') }}" 
                       class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-700 dark:text-gray-300 hover:text-primary-600">
                        <x-heroicon-o-printer class="w-5 h-5 text-primary-500" />
                        <span class="font-medium">Imprimir Formulario</span>
                    </a>

                    <!-- Enlace 4: Devolucion lleva a Responsables-->
                    <a href="{{ \App\Filament\Resources\ResponsableResource::getUrl('index') }}" 
                       class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-700 dark:text-gray-300 hover:text-primary-600">
                       <x-heroicon-o-pencil-square class="w-5 h-5 text-primary-500" />                        
                       <span class="font-medium">Devolucion de Bienes</span>
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>