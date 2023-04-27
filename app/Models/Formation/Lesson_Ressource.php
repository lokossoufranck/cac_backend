<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ressource\Ressource;

class Lesson_Ressource extends Model
{
    use HasFactory;
    protected $fillable = ['id','lesson_id','ressource_id',];
    protected $table = 'lesson_ressource';
    public function ressource()
    {
            return $this->belongsTo(Ressource::class);
    }

    public function lesson()
    {
            return $this->belongsTo(Lesson::class);
    }
}
