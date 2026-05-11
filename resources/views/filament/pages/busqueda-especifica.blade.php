<x-filament-panels::page>
    
    {{-- Esto renderiza automáticamente el cuadro y todos los campos que configuramos en PHP --}}
    {{ $this->form }}

    {{-- Estos son los botones que ejecutan los métodos de descarga --}}
    <div class="flex justify-end gap-4 mt-6">
        <x-filament::button wire:click="descargarExcel" color="success" icon="heroicon-o-table-cells" size="lg">
            Descargar EXCEL
        </x-filament::button>

        <x-filament::button wire:click="descargarPdf" color="danger" icon="heroicon-o-document-text" size="lg">
            Descargar PDF
        </x-filament::button>
    </div>
    <x-filament::section class="mt-8">
        <div class="text-sm text-gray-500">
            <p><strong>Nota:</strong> Este reporte consolidará todos los movimientos
                 (Entregas, Transferencias y Devoluciones) registrados en el sistema dependiendo las especificaciones dadas (SI DEJA EN BLANCO HARA EL REPORTE DE TODOS LOS MOVIMINETOS)</p>
        </div>
    </x-filament::section>
</x-filament-panels::page>