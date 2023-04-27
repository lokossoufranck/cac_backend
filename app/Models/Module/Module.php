<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'description','icon','url_icon', 'url_root',
    'api_url', 'api_username', 'api_password',
    'version', 'actif'];
    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function controllers()
    {
        return $this->belongsToMany(Controllers::class);
    }

   
}
