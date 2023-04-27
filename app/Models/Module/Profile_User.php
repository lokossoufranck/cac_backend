<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile_User extends Model
{
    use HasFactory;
    protected $table = 'profile_user';
    protected $fillable = ['user_id', 'profile_id', 'actif'];

    public function user()
    {
            return $this->belongsTo(User::class);
    }

    public function profile()
    {
            return $this->belongsTo(Profile::class);
    }
}
