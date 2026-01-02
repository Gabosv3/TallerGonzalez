<x-filament-panels::page>
    <x-filament::section>
        <div 
            x-data="{ 
                currentVideo: @if($tutoriales->isNotEmpty()) {
                    src: {{ json_encode($tutoriales->first()->embed_url) }},
                    title: {{ json_encode($tutoriales->first()->titulo) }},
                    desc: {{ json_encode($tutoriales->first()->descripcion) }}
                } @else null @endif,
                search: '',
                playVideo(url, title, desc) {
                    this.currentVideo = {
                        src: url,
                        title: title,
                        desc: desc
                    };
                }
            }"
            class="space-y-6"
        >
            <!-- Reproductor Principal - Estilo Filament -->
            @if($tutoriales->isNotEmpty())
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
                        <!-- Video Area -->
                        <div class="lg:col-span-2 p-0">
                            <div class="relative aspect-video bg-black">
                                <iframe 
                                    src="{{ $tutoriales->first()->embed_url }}"
                                    :src="currentVideo ? currentVideo.src : '{{ $tutoriales->first()->embed_url }}'" 
                                    class="absolute inset-0 w-full h-full" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>

                        <!-- Info Area -->
                        <div class="p-6 bg-white dark:bg-gray-900">
                            <div class="space-y-4">
                                <!-- Badge -->
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-500/10 text-primary-700 dark:text-primary-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                        Reproduciendo ahora
                                    </span>
                                </div>
                                
                                <!-- Título -->
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight" x-text="currentVideo.title"></h2>
                                
                                <!-- Descripción -->
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <p class="text-gray-600 dark:text-gray-300" x-text="currentVideo.desc"></p>
                                </div>
                                
                                
                                
                                <!-- Nota -->
                                <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">💡 Selecciona otro video</span> de la lista para continuar aprendiendo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        No hay tutoriales disponibles
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Los tutoriales se agregarán próximamente.
                    </p>
                </div>
            @endif

            <!-- Lista de Tutoriales -->
            <div class="space-y-4">
                <!-- Header con buscador -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Tutoriales Disponibles
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $tutoriales->count() }} videos disponibles
                        </p>
                    </div>
                    
                    <!-- Buscador estilo Filament -->
                    <div class="w-full sm:w-64">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="search" 
                                placeholder="Buscar tutoriales..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Grid de videos -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
                    @foreach($tutoriales as $tutorial)
                        <div 
                            x-show="search === '' || {{ json_encode(strtolower($tutorial->titulo)) }}.includes(search.toLowerCase()) || {{ json_encode(strtolower($tutorial->descripcion)) }}.includes(search.toLowerCase())"
                            class="group bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:border-primary-500 dark:hover:border-primary-500 transition-colors cursor-pointer"
                            @click="playVideo({{ json_encode($tutorial->embed_url) }}, {{ json_encode($tutorial->titulo) }}, {{ json_encode($tutorial->descripcion) }})"
                            :class="{ 'ring-2 ring-primary-500 border-primary-500': currentVideo && currentVideo.src === {{ json_encode($tutorial->embed_url) }} }"
                        >
                            <!-- Thumbnail -->
                            <div class="relative aspect-video bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                @if($tutorial->thumbnail_path)
                                    <img src="{{ asset('storage/' . $tutorial->thumbnail_path) }}" 
                                         alt="{{ $tutorial->titulo }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Indicador de reproducción -->
                                <div x-show="currentVideo && currentVideo.src === {{ json_encode($tutorial->embed_url) }}"
                                     class="absolute top-2 left-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-primary-500 text-white">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                        Reproduciendo
                                    </span>
                                </div>
                                
                                <!-- Duración -->
                                <div class="absolute bottom-2 right-2">
                                    <span class="px-1.5 py-0.5 text-xs font-medium bg-black/70 text-white rounded">
                                        5:30
                                    </span>
                                </div>
                            </div>

                            <!-- Contenido -->
                            <div class="p-4">
                                <!-- Categoría -->
                                <div class="mb-2">
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                                        Tutorial
                                    </span>
                                </div>
                                
                                <!-- Título -->
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm line-clamp-2 mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    {{ $tutorial->titulo }}
                                </h4>
                                
                                <!-- Descripción -->
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $tutorial->descripcion }}
                                </p>
                                
                                <!-- Footer -->
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $tutorial->created_at->diffForHumans() }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Mensaje si no hay resultados -->
                <div x-show="$el.querySelectorAll('div[style*=\"display: none\"]').length === {{ $tutoriales->count() }} && search !== ''"
                     class="text-center py-8">
                    
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                        No se encontraron resultados
                    </h4>
                    <p class="text-gray-500 dark:text-gray-400">
                        Intenta con otros términos de búsqueda
                    </p>
                </div>
            </div>
        </div>
    </x-filament::section>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .aspect-video {
            aspect-ratio: 16 / 9;
        }
        
        /* Smooth transitions */
        * {
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
    </style>
</x-filament-panels::page>