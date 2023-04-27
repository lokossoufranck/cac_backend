<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControllerErp extends Model
{
    use HasFactory;
    protected $fillable=['nom','icon', 'code', 'actif', 'module_id'];
    protected $table = 'controllers';
    public function Fonctionnalites()
    {
        return $this->belongsToMany(Fonctionnalite::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
