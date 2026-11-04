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
}
