<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;
use App\Models\Module\Profile;

class User extends Authenticatable  implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username','firstname', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '.jpg?s=200&d=mm';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assigning User role
     *
     * @param \App\Models\Role $role
     */
    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function isAdmin()
    {
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function isUser()
    {
        return $this->roles()->where('name', 'User')->exists();
    }

    public function isSupervisor()
    {
        return $this->roles()->where('name', 'Supervisor')->exists();
    }


    
    
    public function site()
    {
            return $this->belongsTo(Site::class);
    }

    // Cette fonction retour le profile d'un utilisateur
    // elle determine les habillitation de l'utilisateur: module,controlleur, fonctions
    public function connected_profile()
    {
       /* $modules =$this->find(1)
        ->join('profile_user','profile_user.user_id','users.id')
        ->join('profiles','profiles.id','profile_user.profile_id')   
        ->join('fonctionnalite_profile','fonctionnalite_profile.profile_id','profile_user.profile_id')
        ->join('fonctionnalites','fonctionnalites.id','fonctionnalite_profile.fonctionnalite_id')
        ->join('controllers','controllers.id','fonctionnalites.controller_id')
        ->join('modules','modules.id','controllers.module_id')
        ->join('menus','menus.module_id','modules.id')
        ->join('sousmenus','sousmenus.menu_id','sousmenus.id')
        ->select('modules.*')
        ->get();*/
        $profiles =Profile_User::
            join('users','users.id','profile_user.user_id')
          ->join('profiles',function($join){
               $join->on("profile_user.profile_id","profiles.id")
                    ->on("profile_user.user_id","=","users.id");
                   }
           )  
          ->join('fonctionnalite_profile','fonctionnalite_profile.profile_id','profile_user.profile_id')
          ->join('fonctionnalites', function($join){
           $join->on("fonctionnalite_profile.profile_id","profiles.id")
           ->on("fonctionnalite_profile.fonctionnalite_id","=","fonctionnalites.id");
          })
          ->join('controllers','controllers.id','fonctionnalites.controller_id')
          ->join('modules','modules.id','controllers.module_id')
          ->join('menus','menus.module_id','modules.id')
          ->join('sousmenus','sousmenus.menu_id','sousmenus.id')
          ->where('profile_user.user_id',Auth::user()->id)
          ->select('modules.*')
          ->get();
        return $profiles;
    }


    public function profiles()
    {
        return $this->belongsToMany(Profile::class);
    }


}
