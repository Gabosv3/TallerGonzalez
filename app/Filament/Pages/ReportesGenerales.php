<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Marca;
use App\Support\ReportData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class ReportesGenerales extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Centro de reportes';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.reportes-generales';
    protected static bool $shouldRegisterNavigation = true;

    public ?array $facturas = [];
    public ?array $productos = [];
    public ?array $clientes = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_reporte') ?? false;
    }

    public function mount(): void
    {
        $this->facturasForm->fill([
            'desde' => now('America/El_Salvador')->startOfMonth()->toDateString(),
            'hasta' => now('America/El_Salvador')->toDateString(),
        ]);
        $this->productosForm->fill();
        $this->clientesForm->fill(['estado' => '1']);
    }

    protected function getForms(): array
    {
        return ['facturasForm', 'productosForm', 'clientesForm'];
    }

    public function facturasForm(Form $form): Form
    {
        return $form
            ->statePath('facturas')
            ->schema([
                Grid::make(['default' => 1, 'md' => 4])->schema([
                    DatePicker::make('desde')->label('Desde')->native(false)->displayFormat('d/m/Y')->live(),
                    DatePicker::make('hasta')->label('Hasta')->native(false)->displayFormat('d/m/Y')->live(),
                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->placeholder('Todos')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => Cliente::query()
                            ->where(fn ($q) => $q->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellido', 'like', "%{$search}%")
                                ->orWhere('razon_social', 'like', "%{$search}%"))
                            ->limit(30)->get()
                            ->mapWithKeys(fn ($c) => [$c->id => ReportData::nombreCliente($c)])->all())
                        ->getOptionLabelUsing(fn ($value) => ReportData::nombreCliente(Cliente::find($value)))
                        ->live(),
                    Select::make('estado')
                        ->label('Estado')
                        ->placeholder('Todos')
                        ->options(['pagada' => 'Pagada', 'pendiente' => 'Pendiente', 'cancelada' => 'Cancelada'])
                        ->live(),
                ]),
            ]);
    }

    public function productosForm(Form $form): Form
    {
        return $form
            ->statePath('productos')
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    Select::make('marca_id')
                        ->label('Marca')
                        ->placeholder('Todas')
                        ->options(fn () => Marca::orderBy('nombre')->pluck('nombre', 'id'))
                        ->searchable()
                        ->live(),
                    Select::make('stock')
                        ->label('Nivel de stock')
                        ->placeholder('Todos')
                        ->options([
                            'bajo' => 'Bajo el mínimo',
                            'agotado' => 'Agotado',
                            'disponible' => 'Sobre el mínimo',
                        ])
                        ->live(),
                ]),
            ]);
    }

    public function clientesForm(Form $form): Form
    {
        return $form
            ->statePath('clientes')
            ->schema([
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    Select::make('estado')
                        ->label('Estado del cliente')
                        ->options(['1' => 'Activos', '0' => 'Inactivos', 'todos' => 'Todos'])
                        ->default('1')
                        ->selectablePlaceholder(false)
                        ->live(),
                    DatePicker::make('desde')->label('Compras desde')->native(false)->displayFormat('d/m/Y')->live(),
                    DatePicker::make('hasta')->label('Compras hasta')->native(false)->displayFormat('d/m/Y')->live(),
                ]),
            ]);
    }

    public function getViewData(): array
    {
        $f = array_filter($this->facturas ?? [], fn ($v) => $v !== null && $v !== '');
        $p = array_filter($this->productos ?? [], fn ($v) => $v !== null && $v !== '');
        $c = array_filter($this->clientes ?? [], fn ($v) => $v !== null && $v !== '');

        return [
            'facturasResumen' => ReportData::facturasResumen($f),
            'productosResumen' => ReportData::productosResumen($p),
            'clientesResumen' => ReportData::clientesResumen($c),
            'facturasQuery' => $f,
            'productosQuery' => $p,
            'clientesQuery' => $c,
        ];
    }
}
