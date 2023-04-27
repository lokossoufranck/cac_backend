<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    use HasFactory;
    protected $fillable = ['nom','code_icao','devise','url_drapeau','actif','',''];
    public function sites()
    {
            return $this->belongsToMany(Site::class);
    }
}
