<x-filament-widgets::widget>
    <div class="kpi-grid">
        @foreach ($kpis as $kpi)
            <a @if ($kpi['url']) href="{{ $kpi['url'] }}" @endif class="kpi kpi--{{ $kpi['color'] }}">
                <div class="kpi-icon">
                    <x-filament::icon :icon="$kpi['icon']" />
                </div>
                <div>
                    <span class="kpi-label">{{ $kpi['label'] }}</span>
                    <strong class="kpi-value">{{ $kpi['value'] }}</strong>
                    <div class="kpi-foot">
                        @if ($kpi['trend'] !== null)
                            <span class="kpi-pill {{ $kpi['trend'] >= 0 ? 'up' : 'down' }}">
                                {{ $kpi['trend'] >= 0 ? '▲' : '▼' }} {{ abs($kpi['trend']) }}%
                            </span>
                        @endif
                        <span>{{ $kpi['foot'] }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-filament-widgets::widget>
