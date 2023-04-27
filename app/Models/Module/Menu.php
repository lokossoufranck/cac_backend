<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = ['nom','icon', 'url', 'actif', 'module_id'];

    public function sousmenus()
    {
        return $this->belongsToMany(Sousmenu::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
