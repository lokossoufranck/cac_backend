<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'site_id', 'actif'];
    public function services()
    {
            return $this->belongsToMany(Service::class);
    }
}
