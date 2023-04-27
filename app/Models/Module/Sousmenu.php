<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sousmenu extends Model
{
    use HasFactory;
    protected $fillable = ['nom','icon', 'url', 'actif', 'menu_id'];
    public function menu()
    {
        return $this->belongsTo(Menu::class);
       
    }
}
