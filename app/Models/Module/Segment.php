<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evenement\Detailsevenement;

class Segment extends Model
{
    use HasFactory;
    protected $fillable=['nom','description','actif','campagne_id'];
    public function detailsevenements(){
        return $this->belongsToMany(Detailsevenement::class);
    }
    public function campagne(){
        return $this->belongsTo(Campagne::class);
    }
}
