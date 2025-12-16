<x-filament-panels::page>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Centro de Reportes</h2>
        <p class="text-gray-600 dark:text-gray-400">Acceso a todos los reportes del sistema con opciones de descarga</p>
    </div>



    <!-- Pestañas de Reportes -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 overflow-x-auto" aria-label="Tabs">
                <button onclick="mostrarTab(event, 'generales')" class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 dark:text-blue-400 whitespace-nowrap">
                    📊 Generales
                </button>
                <button onclick="mostrarTab(event, 'facturas')" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap">
                    📋 Facturas
                </button>
                <button onclick="mostrarTab(event, 'productos')" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap">
                    📦 Productos
                </button>
                <button onclick="mostrarTab(event, 'clientes')" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap">
                    👥 Clientes
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab: Generales -->
    <div id="tab-generales" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total de Facturas</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Factura::count() }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total de Clientes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Cliente::count() }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a3 3 0 003-3v-2a3 3 0 00-3-3H3a3 3 0 00-3 3v2a3 3 0 003 3h3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total de Productos</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Producto::count() }}</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10l8 4M7 11l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Ingresos Totales</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">$ {{ number_format(\App\Models\Factura::sum('total') ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Últimas Facturas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Factura::with('cliente')->latest()->limit(5)->get() as $factura)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $factura->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->cliente?->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">$ {{ number_format($factura->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Completada</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay facturas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab: Facturas -->
    <div id="tab-facturas" class="tab-content hidden">
        <div class="mb-6 flex flex-col md:flex-row gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input type="date" id="desde-facturas" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input type="date" id="hasta-facturas" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cliente</label>
                <select id="cliente-facturas" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Cliente::where('activo', 1)->get() as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="descargarPDFFacturas()" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);" class="inline-flex items-center px-6 py-2 text-white rounded font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar PDF
                </button>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="tabla-facturas">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">ID Factura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Impuesto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Items</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tbody-facturas">
                        @forelse(\App\Models\Factura::with('cliente', 'detalles')->latest()->get() as $factura)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 fila-factura" data-fecha="{{ $factura->created_at->format('Y-m-d') }}" data-cliente="{{ $factura->cliente_id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->cliente?->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">$ {{ number_format($factura->subtotal ?? 0, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">$ {{ number_format($factura->impuesto ?? 0, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">$ {{ number_format($factura->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->detalles->count() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay facturas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab: Productos -->
    <div id="tab-productos" class="tab-content hidden">
        <div class="mb-6 flex flex-col md:flex-row gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input type="date" id="desde-productos" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input type="date" id="hasta-productos" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Marca</label>
                <select id="marca-productos" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    <option value="">Todas</option>
                    @foreach(\App\Models\Marca::all() as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock</label>
                <select id="stock-productos" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    <option value="">Todos</option>
                    <option value="bajo">Stock Bajo</option>
                    <option value="disponible">Stock Disponible</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="descargarPDFProductos()" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);" class="inline-flex items-center px-6 py-2 text-white rounded font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar PDF
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Productos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Producto::count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Productos Activos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Producto::where('activo', 1)->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Stock Bajo Mínimo</p>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ \App\Models\Producto::whereRaw('stock_actual < stock_minimo')->count() }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="tabla-productos">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Marca</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Stock Actual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Mínimo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Máximo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Precio Venta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tbody-productos">
                        @forelse(\App\Models\Producto::with('marca')->where('activo', 1)->get() as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 fila-producto" data-marca="{{ $producto->marca_id }}" data-stock="{{ $producto->stock_actual < $producto->stock_minimo ? 'bajo' : 'disponible' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $producto->codigo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $producto->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $producto->marca?->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $producto->stock_actual < $producto->stock_minimo ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' }}">{{ $producto->stock_actual }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $producto->stock_minimo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $producto->stock_maximo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">$ {{ number_format($producto->precio_venta, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay productos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab: Clientes -->
    <div id="tab-clientes" class="tab-content hidden">
        <div class="mb-6 flex flex-col md:flex-row gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input type="date" id="desde-clientes" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input type="date" id="hasta-clientes" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                <select id="estado-clientes" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="descargarPDFClientes()" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);" class="inline-flex items-center px-6 py-2 text-white rounded font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar PDF
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Clientes</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Cliente::count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Clientes Activos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Cliente::where('activo', 1)->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Compras Totales</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">$ {{ number_format(\DB::table('facturas')->sum('total') ?? 0, 2) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="tabla-clientes">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Dirección</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Facturas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total Gasto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tbody-clientes">
                        @forelse(\App\Models\Cliente::withCount('facturas')->withSum('facturas', 'total')->get() as $cliente)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 fila-cliente" data-estado="{{ $cliente->activo }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $cliente->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $cliente->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $cliente->telefono ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ substr($cliente->direccion ?? 'N/A', 0, 30) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $cliente->facturas_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">$ {{ number_format($cliente->facturas_sum_total ?? 0, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cliente->activo ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay clientes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function mostrarTab(event, tabName) {
            event.preventDefault();
            
            // Ocultar todos los tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.add('hidden'));

            // Quitar clase active de todos los botones
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Mostrar el tab seleccionado
            const selectedTab = document.getElementById('tab-' + tabName);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }

            // Activar el botón seleccionado
            event.target.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            event.target.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }

        // FILTROS PARA FACTURAS
        function filtrarFacturas() {
            const desde = document.getElementById('desde-facturas')?.value || '';
            const hasta = document.getElementById('hasta-facturas')?.value || '';
            const cliente = document.getElementById('cliente-facturas')?.value || '';
            
            const filas = document.querySelectorAll('#tbody-facturas .fila-factura');
            let filasVisibles = 0;

            filas.forEach(fila => {
                let mostrar = true;
                const fechaFila = fila.getAttribute('data-fecha');
                const clienteFila = fila.getAttribute('data-cliente');

                // Filtro por fecha desde
                if (desde && fechaFila < desde) {
                    mostrar = false;
                }

                // Filtro por fecha hasta
                if (hasta && fechaFila > hasta) {
                    mostrar = false;
                }

                // Filtro por cliente
                if (cliente && clienteFila !== cliente) {
                    mostrar = false;
                }

                if (mostrar) {
                    fila.classList.remove('hidden');
                    filasVisibles++;
                } else {
                    fila.classList.add('hidden');
                }
            });

            // Mostrar/ocultar mensaje si no hay resultados
            if (filasVisibles === 0) {
                const tbody = document.getElementById('tbody-facturas');
                if (!tbody.querySelector('.sin-resultados')) {
                    const fila = document.createElement('tr');
                    fila.className = 'sin-resultados';
                    fila.innerHTML = '<td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay facturas que coincidan con los filtros seleccionados</td>';
                    tbody.appendChild(fila);
                }
            } else {
                const filaVacia = document.querySelector('.sin-resultados');
                if (filaVacia) {
                    filaVacia.remove();
                }
            }
        }

        // FILTROS PARA PRODUCTOS
        function filtrarProductos() {
            const marca = document.getElementById('marca-productos')?.value || '';
            const stock = document.getElementById('stock-productos')?.value || '';
            
            const filas = document.querySelectorAll('#tbody-productos .fila-producto');
            let filasVisibles = 0;

            filas.forEach(fila => {
                let mostrar = true;
                const marcaFila = fila.getAttribute('data-marca');
                const stockFila = fila.getAttribute('data-stock');

                // Filtro por marca
                if (marca && marcaFila !== marca) {
                    mostrar = false;
                }

                // Filtro por stock
                if (stock && stockFila !== stock) {
                    mostrar = false;
                }

                if (mostrar) {
                    fila.classList.remove('hidden');
                    filasVisibles++;
                } else {
                    fila.classList.add('hidden');
                }
            });

            // Mostrar/ocultar mensaje si no hay resultados
            if (filasVisibles === 0) {
                const tbody = document.getElementById('tbody-productos');
                if (!tbody.querySelector('.sin-resultados')) {
                    const fila = document.createElement('tr');
                    fila.className = 'sin-resultados';
                    fila.innerHTML = '<td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay productos que coincidan con los filtros seleccionados</td>';
                    tbody.appendChild(fila);
                }
            } else {
                const filaVacia = document.querySelector('.sin-resultados');
                if (filaVacia) {
                    filaVacia.remove();
                }
            }
        }

        // FILTROS PARA CLIENTES
        function filtrarClientes() {
            const estado = document.getElementById('estado-clientes')?.value || '';
            
            const filas = document.querySelectorAll('#tbody-clientes .fila-cliente');
            let filasVisibles = 0;

            filas.forEach(fila => {
                let mostrar = true;
                const estadoFila = fila.getAttribute('data-estado');

                // Filtro por estado
                if (estado !== '' && estadoFila !== estado) {
                    mostrar = false;
                }

                if (mostrar) {
                    fila.classList.remove('hidden');
                    filasVisibles++;
                } else {
                    fila.classList.add('hidden');
                }
            });

            // Mostrar/ocultar mensaje si no hay resultados
            if (filasVisibles === 0) {
                const tbody = document.getElementById('tbody-clientes');
                if (!tbody.querySelector('.sin-resultados')) {
                    const fila = document.createElement('tr');
                    fila.className = 'sin-resultados';
                    fila.innerHTML = '<td colspan="7" class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">No hay clientes que coincidan con los filtros seleccionados</td>';
                    tbody.appendChild(fila);
                }
            } else {
                const filaVacia = document.querySelector('.sin-resultados');
                if (filaVacia) {
                    filaVacia.remove();
                }
            }
        }

        function descargarPDFFacturas() {
            const desde = document.getElementById('desde-facturas')?.value || '';
            const hasta = document.getElementById('hasta-facturas')?.value || '';
            const cliente = document.getElementById('cliente-facturas')?.value || '';
            let url = '{{ route("reports.facturas.pdf") }}';
            let params = new URLSearchParams();
            if (desde) params.append('desde', desde);
            if (hasta) params.append('hasta', hasta);
            if (cliente) params.append('cliente_id', cliente);
            window.location.href = url + (params.toString() ? '?' + params.toString() : '');
        }

        function descargarPDFProductos() {
            const marca = document.getElementById('marca-productos')?.value || '';
            const stock = document.getElementById('stock-productos')?.value || '';
            let url = '{{ route("reports.productos.pdf") }}';
            let params = new URLSearchParams();
            if (marca) params.append('marca_id', marca);
            if (stock) params.append('stock', stock);
            window.location.href = url + (params.toString() ? '?' + params.toString() : '');
        }

        function descargarPDFClientes() {
            const estado = document.getElementById('estado-clientes')?.value || '';
            let url = '{{ route("reports.clientes.pdf") }}';
            let params = new URLSearchParams();
            if (estado !== '') params.append('estado', estado);
            window.location.href = url + (params.toString() ? '?' + params.toString() : '');
        }

        // Agregar event listeners cuando se cargan los filtros
        document.addEventListener('DOMContentLoaded', function() {
            // Facturas
            document.getElementById('desde-facturas')?.addEventListener('change', filtrarFacturas);
            document.getElementById('hasta-facturas')?.addEventListener('change', filtrarFacturas);
            document.getElementById('cliente-facturas')?.addEventListener('change', filtrarFacturas);

            // Productos
            document.getElementById('marca-productos')?.addEventListener('change', filtrarProductos);
            document.getElementById('stock-productos')?.addEventListener('change', filtrarProductos);

            // Clientes
            document.getElementById('estado-clientes')?.addEventListener('change', filtrarClientes);
        });
    </script>
</x-filament-panels::page>
