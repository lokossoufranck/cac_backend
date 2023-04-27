<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson_Modulef extends Model
{
    use HasFactory;
    protected $fillable = ['id','lesson_id','modulef_id',];
    protected $table = 'lesson_modulef';
    public function modulef()
    {
            return $this->belongsTo(Modulef::class);
    }

    public function lesson()
    {
            return $this->belongsTo(Lesson::class);
    }
}
