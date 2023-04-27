<?php

namespace App\Models\Module;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evenement\Dysfonctionnement;

class Campagne extends Model
{
    use HasFactory;

    protected $fillable=['nom','description','actif','site_id'];

    public function site()
    {
        return $this->belongsTo(Site::class);  
    }

    public function dysfonctionnements(){
        return $this->belongsToMany(Dysfonctionnement::class);
    }
    
    public function segments(){
        return $this->belongsToMany(Segment::class);
    }
}
