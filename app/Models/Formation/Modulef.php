<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Whatslearn\Whatslearn;


class Modulef extends Model
{
    use HasFactory;
    protected $fillable = ['id','code','title','short_description','descritpion',
    'prix','duration','level','has_certification',
    'order','status','is_deleted','lang','url_photo_1','url_photo_2','url_photo_2',
    'description_photo_1','description_photo_2'];

    public function formations()
    {
          //  return $this->belongsToMany(Formation::class);
            return $this->belongsToMany(Formation::class)->select(['name as text','id']);
    }

    public function whatslearns()
    {
          //  return $this->belongsToMany(Whatslearn::class);
            return $this->belongsToMany(Whatslearn::class)->select(['name as text','id']);
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
