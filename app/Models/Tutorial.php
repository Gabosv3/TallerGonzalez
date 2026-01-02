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
