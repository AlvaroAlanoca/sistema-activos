<x-filament-widgets::widget>
    <div class="w-full -mt-6 mb-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 py-6 px-4 flex justify-between items-center w-full">

            <a href="{{ route('filament.admin.resources.servicios.create') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #06b6d4; box-shadow: 0 4px 10px -2px rgba(6, 182, 212, 0.4);" 
                     class="w-20 h-20 rounded-full flex items-center justify-center mb-3">
                    <x-filament::icon icon="heroicon-o-arrow-up-tray" class="w-6 h-6 text-white" />
                </div>
                <span class="text-xs font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-cyan-600 transition-colors text-center">Agregar Servicio</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.resources.bien-bajas.index') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #f59e0b; box-shadow: 0 5px 12px -3px rgba(245, 158, 11, 0.4);" 
                     class="w-20 h-20 rounded-full flex items-center justify-center mb-3">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-6 h-6 text-white" />
                </div>
                <span class="text-xs font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-amber-600 transition-colors text-center">Dar baja Bienes</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.pages.busqueda-especifica') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #10b981; box-shadow: 0 4px 10px -2px rgba(16, 185, 129, 0.4);" 
                     class="w-20 h-20 rounded-full flex items-center justify-center mb-3">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-6 h-6 text-white" />
                </div>
                <span class="text-xs font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-emerald-600 transition-colors text-center">Reporte Específico</span>
            </a>

        </div>

    </div>
</x-filament-widgets::widget>