<x-filament-widgets::widget>
    <div class="w-full -mt-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 py-5 px-4 flex justify-between items-center w-full">

            <a href="{{ route('filament.admin.resources.biens.create') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #06b6d4; box-shadow: 0 5px 15px -3px rgba(6, 182, 212, 0.4);" class="w-14 h-14 rounded-full flex items-center justify-center mb-2 transition-all group-hover:shadow-cyan-300">
                    <x-filament::icon icon="heroicon-o-plus-circle" class="w-8 h-8 text-white" />
                </div>
                <span class="text-[11px] font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-cyan-600 transition-colors text-center">Registrar Activo</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.resources.actas.create') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #3b82f6; box-shadow: 0 5px 15px -3px rgba(59, 130, 246, 0.4);" class="w-14 h-14 rounded-full flex items-center justify-center mb-2 transition-all group-hover:shadow-blue-300">
                    <x-filament::icon icon="heroicon-o-document-text" class="w-8 h-8 text-white" />
                </div>
                <span class="text-[11px] font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-blue-600 transition-colors text-center">Generar Acta</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.resources.biens.index') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #0f172a; box-shadow: 0 5px 15px -3px rgba(15, 23, 42, 0.4);" class="w-14 h-14 rounded-full flex items-center justify-center mb-2 transition-all group-hover:shadow-slate-400">
                    <x-filament::icon icon="heroicon-o-archive-box-x-mark" class="w-8 h-8 text-white" />
                </div>
                <span class="text-[11px] font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-slate-900 transition-colors text-center">Dar de Baja</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.resources.actas.index') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #475569; box-shadow: 0 5px 15px -3px rgba(71, 85, 105, 0.4);" class="w-14 h-14 rounded-full flex items-center justify-center mb-2 transition-all group-hover:shadow-slate-300">
                    <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-8 h-8 text-white" />
                </div>
                <span class="text-[11px] font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-slate-700 transition-colors text-center">Ver Historial</span>
            </a>

            <div class="hidden sm:block w-px h-12 bg-slate-100"></div>

            <a href="{{ route('filament.admin.pages.dashboard') }}" class="group flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform duration-200 flex-1">
                <div style="background-color: #10b981; box-shadow: 0 5px 15px -3px rgba(16, 185, 129, 0.4);" class="w-14 h-14 rounded-full flex items-center justify-center mb-2 transition-all group-hover:shadow-emerald-300">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="w-8 h-8 text-white" />
                </div>
                <span class="text-[11px] font-extrabold text-slate-600 uppercase tracking-widest group-hover:text-emerald-600 transition-colors text-center">Reportes</span>
            </a>

        </div>
    </div>
</x-filament-widgets::widget>