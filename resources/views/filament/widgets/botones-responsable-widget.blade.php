<x-filament-widgets::widget>
    <div class="max-w-3xl mx-auto w-full mt-2 mb-6">
        
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-6 md:p-10">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 border border-slate-100 mb-4 shadow-inner">
                    <x-filament::icon icon="heroicon-o-user" class="w-8 h-8 text-slate-400" />
                </div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                    ¡Bienvenido, {{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()->name ?? 'Funcionario' }}!
                </h2>
                <p class="text-slate-500 mt-2 text-[13px] font-medium">
                    Centro de gestión de activos fijos. Seleccione una operación.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

                <a href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" class="group relative flex items-center p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-cyan-300 hover:shadow-lg hover:shadow-cyan-100/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
                    
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div style="background-color: #06b6d4;" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <x-filament::icon icon="heroicon-o-archive-box" class="w-5 h-5 text-white" />
                    </div>

                    <div class="ml-3 flex-1">
                        <h3 class="text-[13px] font-extrabold text-slate-700 group-hover:text-cyan-600 transition-colors uppercase tracking-wider">Mis Activos</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Ver inventario en custodia.</p>
                    </div>

                    <x-filament::icon icon="heroicon-m-chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-cyan-500 group-hover:translate-x-1 transition-all" />
                </a>

                <a href="{{ \App\Filament\Resources\BienResource::getUrl('index') }}" class="group relative flex items-center p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
                    
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div style="background-color: #6366f1;" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <x-filament::icon icon="heroicon-o-arrows-right-left" class="w-5 h-5 text-white" />
                    </div>

                    <div class="ml-3 flex-1">
                        <h3 class="text-[13px] font-extrabold text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-wider">Transferencias</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Traspasar equipos a terceros.</p>
                    </div>

                    <x-filament::icon icon="heroicon-m-chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all" />
                </a>

            </div>

            <div class="bg-slate-50 rounded-2xl p-4 flex gap-3 items-start border border-slate-100">
                <div class="mt-0.5">
                    <x-filament::icon icon="heroicon-s-information-circle" class="w-5 h-5 text-slate-400" />
                </div>
                <p class="text-[12px] text-slate-600 leading-relaxed">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Tip rápido:</span> Para transferir, ingrese a la opción, marque las casillas de los equipos deseados en la tabla y presione el botón <span class="font-bold text-indigo-500">"Transferir Bienes"</span>.
                </p>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>