<?php

namespace App\Exports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

/**
 * Exportador XLSX genérico: encabezado con estilo, celdas tipadas y columnas de moneda.
 */
class TablaExport
{
    /**
     * @param  array<int, string>  $encabezados
     * @param  iterable<array<int, mixed>>  $filas
     * @param  array<int, int>  $anchos  Ancho por columna (caracteres)
     * @param  array<int, int>  $columnasMoneda  Índices (0-based) con formato de dinero
     */
    public static function generar(string $nombre, array $encabezados, iterable $filas, array $anchos = [], array $columnasMoneda = []): string
    {
        $dir = storage_path('app/exports');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $ruta = $dir . '/' . $nombre . '-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.xlsx';

        $writer = new Writer();
        $options = $writer->getOptions();
        foreach ($anchos as $i => $ancho) {
            $options->setColumnWidth($ancho, $i + 1);
        }
        if (method_exists($options, 'setFreezeRow')) {
            $options->setFreezeRow(2);
        }

        $writer->openToFile($ruta);

        $estiloEncabezado = (new Style())
            ->setBackgroundColor('1F2937')
            ->setFontColor(Color::WHITE)
            ->setFontBold();
        $writer->addRow(Row::fromValues($encabezados, $estiloEncabezado));

        $estiloMoneda = (new Style())->setFormat('$#,##0.00');

        foreach ($filas as $fila) {
            $fila = array_values($fila);
            $row = Row::fromValues($fila);
            foreach ($columnasMoneda as $i) {
                if (isset($fila[$i]) && is_numeric($fila[$i])) {
                    $row->setCellAtIndex(\OpenSpout\Common\Entity\Cell::fromValue((float) $fila[$i], $estiloMoneda), $i);
                }
            }
            $writer->addRow($row);
        }

        $writer->close();

        return $ruta;
    }
}
