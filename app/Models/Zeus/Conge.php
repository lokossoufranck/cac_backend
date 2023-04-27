<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;
    protected $fillable=['date_debut', 'date_fin', 'date_demande', 'date_reprise', 'nombre_jour', 'personnel_id', 'conge_type_id'];
}

