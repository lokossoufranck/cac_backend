<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonctionnalite_Profile extends Model
{
    use HasFactory;
    protected $table = 'fonctionnalite_profile';
    protected $fillable = [ 'actif', 'profile_id', 'fonctionnalite_id'];
    public function profile()
    {
            return $this->belongsTo(Profile::class);
    }

    public function fonctionnalite()
    {
            return $this->belongsTo(Fonctionnalite::class);
    }
}
