<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonctionnalite extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'actif', 'controller_id'];

    public function controller()
    {
        return $this->belongsTo(Controller::class);
    }

    public function profiles()
    {
            return $this->belongsToMany(Profile::class);
    }

    public function fonctionnalite_profile()
    {
            return $this->belongsToMany(Fonctionnalite_Profile::class);
    }
}
