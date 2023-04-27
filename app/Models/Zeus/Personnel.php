<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;
    protected $fillable=['nom','prenoms','numero_cnss','nb_enfants_a_charge','url_photo_identite','matricule', 'adresse', 'telephone', 'domaine_etude', 'date_naissance', 'sexe', 'date_entree', 'fonction_id', 'personne_a_contacter', 'personnel_status_id', 'domiciliation_irrevocable'];
}
