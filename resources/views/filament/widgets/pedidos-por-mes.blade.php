<div class="filament-widget">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">Pedidos (últimos 6 meses)</div>
        </div>
        <div class="mt-3">
            <div wire:ignore>
                <canvas id="pedidosChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const labels = {!! json_encode($this->labels) !!};
            const data = {!! json_encode($this->data) !!};

            function initChart() {
                const ctx = document.getElementById('pedidosChart');
                if (!ctx) return;
                if (ctx.__chart_initialized) return;
                ctx.__chart_initialized = true;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pedidos',
                            data: data,
                            backgroundColor: 'rgba(59,130,246,0.2)',
                            borderColor: 'rgba(59,130,246,1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', initChart);
            document.addEventListener('livewire:load', initChart);
            document.addEventListener('livewire:update', initChart);
            setTimeout(initChart, 150);
        })();
    </script>
</div>
