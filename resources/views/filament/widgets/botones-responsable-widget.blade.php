<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-6">

            {{-- 1. CABECERA: Alineada a la izquierda con línea divisoria --}}
            <div class="pb-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    ¡Hola, {{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()->name }}!
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Gestione los activos fijos bajo su custodia en el SEDUCA. ¿Qué desea hacer hoy?
                </p>
            </div>

            {{-- 2. TARJETAS DE ACCIÓN: En 2 columnas para PC, 1 columna para celular --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                {{-- Tarjeta A: Consultar Activos --}}
                <a href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" 
                   class="relative flex items-center gap-4 p-5 transition-all bg-gray-50 border border-gray-200 rounded-xl hover:bg-white hover:shadow-md hover:border-blue-500 hover:ring-1 hover:ring-blue-500 group dark:bg-gray-800/50 dark:border-gray-700 dark:hover:bg-gray-800 dark:hover:border-blue-400">
                    
                    {{-- Icono --}}
                    <div class="flex items-center justify-center w-12 h-12 transition-colors rounded-full shrink-0 bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-900/50 dark:text-blue-400">
                        <x-heroicon-o-archive-box class="w-6 h-6" />
                    </div>
                    
                    {{-- Texto --}}
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 transition-colors dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            Mis Activos Asignados
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Verifique la lista de bienes bajo su custodia.
                        </p>
                    </div>

                    {{-- Flecha indicadora (Se mueve al pasar el mouse) --}}
                    <div class="text-gray-400 transition-transform shrink-0 group-hover:translate-x-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        <x-heroicon-m-chevron-right class="w-6 h-6" />
                    </div>
                </a>

                {{-- Tarjeta B: Realizar Transferencia --}}
                <a href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" 
                   class="relative flex items-center gap-4 p-5 transition-all bg-gray-50 border border-gray-200 rounded-xl hover:bg-white hover:shadow-md hover:border-orange-500 hover:ring-1 hover:ring-orange-500 group dark:bg-gray-800/50 dark:border-gray-700 dark:hover:bg-gray-800 dark:hover:border-orange-400">
                    
                    {{-- Icono --}}
                    <div class="flex items-center justify-center w-12 h-12 transition-colors rounded-full shrink-0 bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white dark:bg-orange-900/50 dark:text-orange-400">
                        <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                    </div>
                    
                    {{-- Texto --}}
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 transition-colors dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400">
                            Realizar Transferencia
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Traspase un activo fijo a otro funcionario.
                        </p>
                    </div>

                    {{-- Flecha indicadora --}}
                    <div class="text-gray-400 transition-transform shrink-0 group-hover:translate-x-1 group-hover:text-orange-600 dark:group-hover:text-orange-400">
                        <x-heroicon-m-chevron-right class="w-6 h-6" />
                    </div>
                </a>

            </div>
            
            {{-- 3. CAJA DE INFORMACIÓN (Tip) --}}
            <div class="flex items-start gap-3 p-4 mt-2 text-sm text-gray-700 bg-blue-50 rounded-xl dark:bg-gray-900 dark:text-gray-300 dark:border dark:border-gray-800">
                <x-heroicon-m-information-circle class="w-5 h-5 mt-0.5 text-blue-500 shrink-0" />
                <p>
                    <strong>Tip rápido:</strong> Para transferir, ingrese a la opción, marque las casillas de los equipos deseados en la tabla y presione el botón <i>"Transferir Bienes"</i>.
                </p>
            </div>
            
        </div>
    </x-filament::section>
</x-filament-widgets::widget>