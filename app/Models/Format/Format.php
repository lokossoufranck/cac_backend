<?php

namespace App\Models\Format;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','code','status'];
   
}
