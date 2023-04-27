<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteBancaire extends Model
{
    use HasFactory;
    protected $fillable=["numero", "banque_id", "personnel_id"];
}
