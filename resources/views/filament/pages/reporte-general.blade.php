<x-filament-panels::page>
    
    <div class="mb-6">
        {{-- Renderiza el formulario de fechas --}}
        {{ $this->form }}
    </div>

    {{-- Botones de acción alineados a la derecha --}}
    <div class="flex justify-end gap-4">
        <x-filament::button 
            wire:click="descargarExcel" 
            color="success" 
            icon="heroicon-o-table-cells" 
            size="lg"
        >
            Descargar EXCEL
        </x-filament::button>

        <x-filament::button 
            wire:click="descargarPdf" 
            color="danger" 
            icon="heroicon-o-document-text" 
            size="lg"
        >
            Descargar PDF
        </x-filament::button>
    </div>

    <x-filament::section class="mt-8">
        <div class="text-sm text-gray-500">
            <p><strong>Nota:</strong> Este reporte consolidará todos los movimientos (Entregas, Transferencias y Devoluciones) registrados en el sistema dentro del rango de fechas seleccionado, incluyendo a todos los responsables y tipos de bienes.</p>
        </div>
    </x-filament::section>

</x-filament-panels::page>