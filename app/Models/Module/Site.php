<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = [ 'nom','adresse','sigle' ,'url_logo', 'url_header_1', 'url_header_2', 'url_footer_1', 'url_footer_2', 'tel1', 'tel2', 'email', 'site_web', 'is_siege', 'actif', 'pays_id'];

    public function profiles()
    {
            return $this->belongsToMany(Profile::class);
    }

    public function users()
    {
            return $this->belongsToMany(Users::class);
    }

    public function departements()
    {
        return $this->belongsToMany(Departement::class);
    }
}
