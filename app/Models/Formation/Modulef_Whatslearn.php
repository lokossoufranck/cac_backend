<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulef_Whatslearn extends Model
{
    use HasFactory;
    protected $fillable = ['id','whatslearn_id','modulef_id',];
    protected $table = 'modulef_whatslearn';
    public function modulef()
    {
            return $this->belongsTo(Modulef::class);
    }

    public function whatslearn()
    {
            return $this->belongsTo(Whatslearn::class);
    }
}
