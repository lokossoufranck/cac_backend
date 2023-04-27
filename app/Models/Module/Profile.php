<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'url','site_id', 'actif'];
    public function fonctionnalites()
    {
            return $this->belongsToMany(Fonctionnalite::class);
    }


    public function fonctionnalite_profile()
    {
            return $this->belongsToMany(Fonctionnalite_Profile::class);
    }

    
    public function users()
    {
            return $this->belongsToMany(User::class);
    }


    public function user_profiles()
    {
            return $this->belongsToMany(User_Profile::class);
    }


    public function site()
    {
            return $this->belongsTo(Site::class);
    }
}
