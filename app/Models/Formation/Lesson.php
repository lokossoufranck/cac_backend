<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ressource\Ressource;


class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['id','code','title','short_description','descritpion',
    'content','duration','level','has_certification',
    'order','status','is_deleted','lang','url_photo_1','url_photo_2','url_photo_2',
    'description_photo_1','description_photo_2'];

    public function ressources()
    {
            return $this->belongsToMany(Ressource::class)->select(['name as text','id']);
    }

    public function modulefs()
    {

            return $this->belongsToMany(Modulef::class)->select(['name as text','id']);
    }


    public function lesson_modulefs()
    {
            return $this->belongsToMany(Lesson_Modulef::class);
    }

    public function lesson_ressources()
    {
            return $this->belongsToMany(Lesson_Ressource::class);
    }
   
}
