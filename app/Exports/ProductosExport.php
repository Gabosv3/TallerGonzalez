<?php

namespace App\Exports;

use App\Models\Producto;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class ProductosExport
{
    protected $productos;

    public function __construct($productos = null)
    {
        $this->productos = $productos;
    }

    public function export()
    {
        $writer = new Writer();
        $fileName = 'productos-' . now()->format('Ymd-His') . '.xlsx';
        $filePath = storage_path('app/exports/' . $fileName);

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0777, true);
        }

        $writer->openToFile($filePath);

        // Header style
        $headerStyle = (new Style())
            ->setBackgroundColor('366092')
            ->setFontColor(Color::WHITE)
            ->setFontBold();

        // Data rows
        $productos = $this->productos ?? Producto::with(['marca', 'tipoProducto'])->get();

        // Header row
        $headerCells = [
            new StringCell('ID', $headerStyle),
            new StringCell('Código', $headerStyle),
            new StringCell('Nombre', $headerStyle),
            new StringCell('Tipo', $headerStyle),
            new StringCell('Marca', $headerStyle),
            new StringCell('Descripción', $headerStyle),
            new StringCell('Precio Compra', $headerStyle),
            new StringCell('Precio Venta', $headerStyle),
            new StringCell('Precio + IVA', $headerStyle),
            new StringCell('Stock Actual', $headerStyle),
            new StringCell('Stock Mínimo', $headerStyle),
            new StringCell('Stock Máximo', $headerStyle),
            new StringCell('Activo', $headerStyle),
            new StringCell('Creado', $headerStyle),
            new StringCell('Actualizado', $headerStyle)
        ];
        $writer->addRow(new Row($headerCells));

        // Data rows
        foreach ($productos as $producto) {
            $cells = [
                new StringCell((string)$producto->id, null),
                new StringCell($producto->codigo, null),
                new StringCell($producto->nombre, null),
                new StringCell($producto->tipoProducto?->nombre ?? 'N/A', null),
                new StringCell($producto->marca?->nombre ?? 'N/A', null),
                new StringCell($producto->descripcion ?? '', null),
                new StringCell((string)$producto->precio_compra, null),
                new StringCell((string)$producto->precio_venta, null),
                new StringCell((string)round($producto->precio_venta * 1.13, 2), null),
                new StringCell((string)$producto->stock_actual, null),
                new StringCell((string)$producto->stock_minimo, null),
                new StringCell((string)$producto->stock_maximo, null),
                new StringCell($producto->activo ? 'Sí' : 'No', null),
                new StringCell($producto->created_at?->format('d/m/Y H:i') ?? '', null),
                new StringCell($producto->updated_at?->format('d/m/Y H:i') ?? '', null)
            ];
            $writer->addRow(new Row($cells));
        }

        $writer->close();

        return $filePath;
    }
}
