<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','code','order','status','lang','url_photo_1','url_photo_2',
    'description_photo_1','description_photo_2'];

    public function modulefs()
    {
            return $this->belongsToMany(Modulef::class);
    }


    public function modulef_whatslearns()
    {
            return $this->belongsToMany(Modulef_Whatslearn::class);
    }

    public function formation_modulefs()
    {
            return $this->belongsToMany(Formation_Modulef::class);
    }
   
}
