<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('titulo')</title>
    <style>
        @page { margin: 90px 36px 56px 36px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; margin: 0; }

        header { position: fixed; top: -66px; left: 0; right: 0; height: 56px; border-bottom: 2px solid #f59e0b; }
        header table { width: 100%; border-collapse: collapse; }
        header td { vertical-align: middle; padding: 0; border: 0; }
        .marca { font-size: 15px; font-weight: bold; color: #111827; }
        .marca small { display: block; font-size: 9px; font-weight: normal; color: #6b7280; margin-top: 2px; }
        .doc { text-align: right; font-size: 9px; color: #6b7280; }
        .doc strong { display: block; font-size: 13px; color: #111827; margin-bottom: 2px; }

        footer { position: fixed; bottom: -36px; left: 0; right: 0; height: 24px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 8.5px; color: #9ca3af; }
        footer .der { float: right; }

        .filtros { margin: 0 0 12px; padding: 7px 10px; background: #f9fafb; border: 1px solid #e5e7eb; color: #4b5563; }
        .filtros b { color: #111827; }

        .kpis { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 14px; }
        .kpis td { width: 25%; padding: 9px 11px; background: #f9fafb; border: 1px solid #e5e7eb; border-top: 3px solid #f59e0b; }
        .kpis .v { display: block; font-size: 15px; font-weight: bold; color: #111827; }
        .kpis .l { display: block; font-size: 8.5px; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 3px; }

        table.datos { width: 100%; border-collapse: collapse; }
        table.datos th { background: #1f2937; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; text-transform: uppercase; letter-spacing: .3px; }
        table.datos td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        table.datos tbody tr:nth-child(even) td { background: #f9fafb; }
        table.datos tfoot td { padding: 8px; font-weight: bold; background: #f3f4f6; border-top: 2px solid #1f2937; }
        .r { text-align: right; }
        .c { text-align: center; }
        .vacio { padding: 24px; text-align: center; color: #9ca3af; }

        .badge { padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .ok { background: #dcfce7; color: #166534; }
        .warn { background: #fef3c7; color: #92400e; }
        .bad { background: #fee2e2; color: #991b1b; }
        .gris { background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="width: 60px;">
                    @if (file_exists(public_path('assets/img/tallergonzalez.png')))
                        <img src="{{ public_path('assets/img/tallergonzalez.png') }}" style="height: 46px;">
                    @endif
                </td>
                <td class="marca">Taller González<small>Sistema de gestión de inventario y facturación</small></td>
                <td class="doc"><strong>@yield('titulo')</strong>Generado el {{ now('America/El_Salvador')->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </header>

    <footer>
        Taller González · Documento generado automáticamente
        <span class="der">{{ auth()->user()?->name }}</span>
    </footer>

    <main>
        @yield('contenido')
    </main>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans');
            $pdf->page_text($pdf->get_width() - 90, $pdf->get_height() - 28, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 8, [0.61, 0.64, 0.69]);
        }
    </script>
</body>
</html>
