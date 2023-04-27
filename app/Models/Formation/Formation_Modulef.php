<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation_Modulef extends Model
{
    use HasFactory;
    protected $fillable = ['id','formation_id','modulef_id'];
    protected $table = 'formation_modulef';
    public function modulef()
    {
            return $this->belongsTo(Modulef::class);
    }

    public function formation()
    {
            return $this->belongsTo(Formation::class);
    }
}
