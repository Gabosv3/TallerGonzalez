<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;



class Categoria extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['nombre', 'descripcion'];

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}

