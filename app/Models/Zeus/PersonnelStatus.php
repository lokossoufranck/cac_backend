<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelStatus extends Model
{
    use HasFactory;
    protected $fillable=['libelle'];
}
