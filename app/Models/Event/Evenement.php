<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable=['date_debut','date_fin','nb_agent_impacte','details'];

    public function detailsEvenements()
    {
            return $this->belongsToMany(DetailsEvenement::class);
    }
}
