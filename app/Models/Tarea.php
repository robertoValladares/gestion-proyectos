<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyecto_id',
        'titulo',
        'descripcion',
        'completada',
        'fecha_vencimiento'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
