<?php

namespace App\Filament\Pages;

use App\Models\Tutorial;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class VerTutoriales extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Tutoriales';
    protected static ?string $title = 'Tutoriales del Sistema';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.ver-tutoriales';

    public $tutorialActivo = null;

    public function getViewData(): array
    {
        return [
            'tutoriales' => Tutorial::where('activo', true)
                ->orderBy('orden')
                ->get(),
        ];
    }
}
