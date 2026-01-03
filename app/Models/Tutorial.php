<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'video_url',
        'video_path',
        'thumbnail_path',
        'orden',
        'activo',
        'tipo_origen',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'video_path' => 'nullable|string|max:255',
            'thumbnail_path' => 'nullable|string|max:255',
            'orden' => 'integer|min:0',
            'activo' => 'boolean',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'titulo.required' => 'El título del tutorial es obligatorio.',
            'titulo.max' => 'El título no puede exceder 255 caracteres.',
            'video_url.url' => 'La URL del video debe ser válida.',
            'video_url.max' => 'La URL no puede exceder 500 caracteres.',
            'video_path.max' => 'La ruta del video no puede exceder 255 caracteres.',
            'thumbnail_path.max' => 'La ruta del thumbnail no puede exceder 255 caracteres.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.',
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function getTipoOrigenAttribute(): string
    {
        if (!empty($this->video_path)) {
            return 'local';
        }
        return 'url';
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->video_path) {
            return asset('storage/' . $this->video_path);
        }

        if (!$this->video_url) {
            return null;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Vimeo
        if (preg_match('/(?:vimeo\.com\/)(\d+)/', $this->video_url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $this->video_url;
    }

    public function getEsLocalAttribute(): bool
    {
        return !empty($this->video_path);
    }
}
