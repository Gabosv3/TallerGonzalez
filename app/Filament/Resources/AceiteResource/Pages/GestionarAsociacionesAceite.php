<?php

namespace App\Filament\Resources\AceiteResource\Pages;

use App\Filament\Resources\AceiteResource;
use App\Models\Aceite;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class GestionarAsociacionesAceite extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AceiteResource::class;
    protected static string $view = 'filament.resources.aceite-resource.pages.gestionar-asociaciones-aceite';

    public ?array $data = [];
    public Aceite $aceite;

    public function mount(Aceite $aceite): void
    {
        $this->aceite = $aceite;
        
        $asociadosActuales = $aceite->aceitesAsociados->pluck('id')->toArray();
        
        $this->form->fill([
            'aceites_seleccionados' => $asociadosActuales,
        ]);
    }

    public function form(Form $form): Form
    {
        $aceitesDisponibles = Aceite::where('id', '!=', $this->aceite->id)
            ->where(function ($query) {
                $query->whereNull('producto_padre_id')
                      ->orWhere('producto_padre_id', '!=', $this->aceite->id);
            })
            ->with(['producto', 'marca'])
            ->get()
            ->mapWithKeys(function ($aceite) {
                return [$aceite->id => "{$aceite->producto->nombre} - {$aceite->marca->nombre} {$aceite->viscosidad}"];
            })
            ->toArray();

        return $form
            ->schema([
                Section::make("Gestionar asociaciones para: {$this->aceite->producto->nombre}")
                    ->description('Selecciona los aceites que quieres asociar a este grupo')
                    ->schema([
                        Select::make('aceites_seleccionados')
                            ->label('Aceites a asociar')
                            ->options($aceitesDisponibles)
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Selecciona los aceites que pertenecerán a este grupo')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function guardarAsociaciones(): void
    {
        $data = $this->form->getState();
        $aceitesSeleccionados = $data['aceites_seleccionados'] ?? [];

        // Remover todas las asociaciones actuales de este agrupador
        Aceite::where('producto_padre_id', $this->aceite->id)
            ->update(['producto_padre_id' => null]);

        // Agregar nuevas asociaciones
        if (!empty($aceitesSeleccionados)) {
            Aceite::whereIn('id', $aceitesSeleccionados)
                ->update(['producto_padre_id' => $this->aceite->id]);
        }

        // Sincronizar stock del agrupador
        $this->aceite->refresh();
        $this->aceite->sincronizarStock();

        Notification::make()
            ->title('Asociaciones actualizadas correctamente')
            ->success()
            ->body('Los aceites han sido asociados al grupo y el stock ha sido sincronizado.')
            ->send();

        $this->redirect(AceiteResource::getUrl('edit', [$this->aceite->id]));
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Agrupador: {$this->aceite->producto->nombre} - Stock consolidado: {$this->aceite->stock_total_asociados} unidades";
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('guardar')
                ->label('Guardar Asociaciones')
                ->submit('guardarAsociaciones')
                ->color('primary'),

            \Filament\Actions\Action::make('cancelar')
                ->label('Cancelar')
                ->url(AceiteResource::getUrl('edit', [$this->aceite->id]))
                ->color('gray'),
        ];
    }

    // CORRECCIÓN: Método actualizado con parámetros
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false; // No mostrar en navegación principal
    }

    // Agregar estos métodos para completar la implementación
    public static function getNavigationLabel(): string
    {
        return 'Gestionar Asociaciones';
    }

    public function getTitle(): string
    {
        return 'Gestionar Asociaciones de Aceite';
    }
}