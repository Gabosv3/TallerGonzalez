<div class="filament-widget">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">📦 Productos por Marca</div>
        </div>
        
        @if($this->hasData && !empty($this->labels))
            <div class="mt-3" style="height:280px">
                <div wire:ignore>
                    <canvas id="productosMarcaChart"></canvas>
                </div>
            </div>
        @else
            <div class="flex items-center justify-center" style="height:280px">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">No hay datos disponibles</p>
                    <p class="text-xs mt-1">Asigna productos a marcas para ver el gráfico</p>
                </div>
            </div>
        @endif
    </div>

    @if($this->hasData && !empty($this->labels))
        <script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/chart.min.js"></script>
        <script>
            (function () {
                const labels = @json($this->labels);
                const data = @json($this->data);

                console.log('Labels:', labels);
                console.log('Data:', data);

                function initChart() {
                    const ctx = document.getElementById('productosMarcaChart');
                    if (!ctx) {
                        console.log('Canvas no encontrado');
                        return;
                    }
                    if (window.productosMarcaChart instanceof Chart) {
                        window.productosMarcaChart.destroy();
                    }

                    window.productosMarcaChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#60A5FA','#34D399','#FBBF24','#F472B6','#A78BFA',
                                    '#F87171','#94A3B8','#FB923C','#EC4899','#06B6D4'
                                ],
                                borderColor: '#fff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initChart);
                } else {
                    initChart();
                }
                
                document.addEventListener('livewire:navigated', initChart);
                window.addEventListener('livewire:updated', initChart);
            })();
        </script>
    @endif
</div>
