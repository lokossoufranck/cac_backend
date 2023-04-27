<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $fillable =
     ['id', 'action', 'enregistrement', 'user_id', 'created_at', 'updated_at'];
     
    public function user()
    {
            return $this->belongsTo(User::class);
    }
}
