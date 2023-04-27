<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\Module\Campagne;
class Dysfonctionnement extends Model
{
    use HasFactory;
    protected $fillable=['id','nom','description','actif','campagne_id','porte_id'];
    public function porte()
    {
        return $this->belongsTo(Porte::class);
       
    }
   public function campagne()
    {
        return $this->belongsTo(Campagne::class);
       
    }
    public function dysfonctionnement()
    {
            return $this->belongsToMany(Dysfonctionnement::class);
    }
}
