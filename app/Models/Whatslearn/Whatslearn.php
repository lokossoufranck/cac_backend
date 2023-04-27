<?php

namespace App\Models\Whatslearn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatslearn extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','icon'];
   
}
