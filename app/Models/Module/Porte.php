<?php

namespace App\Models\Module;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Porte extends Model
{
    use HasFactory;

    protected $fillable=['nom','actif'];

}
