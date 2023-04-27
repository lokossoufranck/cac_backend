<?php

namespace App\Models\Ressource;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressource extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','format_id','code','url','data',
    'description','adresse'];
   
}
