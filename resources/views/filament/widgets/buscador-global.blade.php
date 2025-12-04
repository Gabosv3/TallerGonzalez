<div class="px-4 py-6">
    <x-filament::section>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">🔍 Buscador Global</label>
                <input 
                    type="text"
                    wire:model.live="searchTerm"
                    placeholder="Busca productos, clientes, marcas, proveedores..."
                    class="w-full px-4 py-2 mt-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                />
            </div>

            @if($searchTerm && count($resultados) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    @foreach($resultados as $resultado)
                        <a href="{{ $resultado['url'] }}" 
                           class="p-3 rounded-lg border-2 border-gray-200 hover:border-{{ $resultado['color'] }}-500 transition-all hover:shadow-lg"
                           target="_blank">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-{{ str_replace('heroicon-o-', '', $resultado['icono']) }} 
                                        class="h-5 w-5 text-{{ $resultado['color'] }}-600" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $resultado['titulo'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $resultado['tipo'] }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $resultado['subtitulo'] }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif($searchTerm && count($resultados) == 0)
                <div class="text-center py-8 text-gray-500">
                    <p class="text-sm">No se encontraron resultados para "{{ $searchTerm }}"</p>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p class="text-sm">Escribe al menos 2 caracteres para buscar</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</div>
