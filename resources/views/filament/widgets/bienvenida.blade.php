<x-filament-widgets::widget>
    <div class="dash-hero">
        <div>
            <h2>{{ $saludo }}, {{ $nombre }}</h2>
            <p>{{ $fecha }} · Resumen de la operación del taller</p>
        </div>

        @if (count($acciones))
            <div class="dash-hero-actions">
                @foreach ($acciones as $accion)
                    <a href="{{ $accion['url'] }}">
                        <x-filament::icon :icon="$accion['icon']" />
                        {{ $accion['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
