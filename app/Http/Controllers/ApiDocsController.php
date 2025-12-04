<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ApiDocsController extends Controller
{
    public function ui()
    {
        return view('api.docs');
    }

    public function spec()
    {
        $path = base_path('docs/openapi.yaml');
        if (! File::exists($path)) {
            abort(404);
        }
        return response()->file($path, ['Content-Type' => 'application/x-yaml']);
    }

    /**
     * Devuelve el spec con numeración de líneas (útil para depuración de YAML en Swagger UI)
     */
    public function specDebug()
    {
        $path = base_path('docs/openapi.yaml');
        if (! File::exists($path)) {
            abort(404);
        }

        $lines = explode("\n", File::get($path));
        $output = "<html><body><pre style=\"font-size:12px;line-height:1.2;\">";
        foreach ($lines as $i => $line) {
            $num = str_pad($i + 1, 4, ' ', STR_PAD_LEFT);
            $output .= "<span style=\"color:#888;\">{$num} | </span>" . htmlspecialchars($line) . "\n";
        }
        $output .= "</pre></body></html>";

        return response($output, 200, ['Content-Type' => 'text/html']);
    }
}
