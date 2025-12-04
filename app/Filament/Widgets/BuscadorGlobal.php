<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Marca;
use App\Models\Pedido;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class BuscadorGlobal extends Widget
{
    protected static string $view = 'filament.widgets.buscador-global';
    protected static ?int $sort = 1;

    public string $searchTerm = '';
    public Collection $resultados;

    public function mount(): void
    {
        $this->resultados = collect();
    }

    public function updatedSearchTerm($value): void
    {
        if (strlen($value) < 2) {
            $this->resultados = collect();
            return;
        }

        $this->resultados = collect();

        // Buscar Productos
        $productos = Producto::where('nombre', 'like', "%{$value}%")
            ->orWhere('codigo', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'tipo' => 'Producto',
                'titulo' => $p->nombre,
                'subtitulo' => "Código: {$p->codigo}",
                'url' => route('filament.administrativo.resources.productos.edit', $p->id),
                'icono' => 'heroicon-o-cube',
                'color' => 'info'
            ]);

        // Buscar Clientes
        $clientes = Cliente::where('nombre', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%")
            ->orWhere('dui', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'tipo' => 'Cliente',
                'titulo' => $c->nombre,
                'subtitulo' => $c->email ?: $c->dui,
                'url' => route('filament.administrativo.resources.clientes.edit', $c->id),
                'icono' => 'heroicon-o-user-group',
                'color' => 'success'
            ]);

        // Buscar Marcas
        $marcas = Marca::where('nombre', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->map(fn ($m) => [
                'tipo' => 'Marca',
                'titulo' => $m->nombre,
                'subtitulo' => $m->pais_origen ?: 'Sin país',
                'url' => route('filament.administrativo.resources.marcas.edit', $m->id),
                'icono' => 'heroicon-o-tag',
                'color' => 'warning'
            ]);

        // Buscar Proveedores
        $proveedores = Proveedor::where('nombre', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'tipo' => 'Proveedor',
                'titulo' => $p->nombre,
                'subtitulo' => $p->email ?: $p->telefono,
                'url' => route('filament.administrativo.resources.proveedores.edit', $p->id),
                'icono' => 'heroicon-o-building-storefront',
                'color' => 'danger'
            ]);

        // Buscar Pedidos
        $pedidos = Pedido::where('numero_pedido', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->map(fn ($pd) => [
                'tipo' => 'Pedido',
                'titulo' => "Pedido #{$pd->numero_pedido}",
                'subtitulo' => $pd->proveedor?->nombre ?: 'Sin proveedor',
                'url' => route('filament.administrativo.resources.pedidos.edit', $pd->id),
                'icono' => 'heroicon-o-clipboard-document-list',
                'color' => 'primary'
            ]);

        $this->resultados = $productos->concat($clientes)
            ->concat($marcas)
            ->concat($proveedores)
            ->concat($pedidos);
    }
}
