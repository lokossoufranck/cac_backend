<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    use HasFactory;
    protected $fillable=['libelle', 'jour_ferie', 'prime_anciennete', 'calcul_smic', 'variable_prorata', 'heure_presence', 'mode_calcul_id'];
}
