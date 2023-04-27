<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Module\Profile_User;
use App\Models\Module\Sousmenu;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

        /**
         * Get the login username to be used by the controller.
         *
         * @return string
         */
      

        public function login(Request $request)
        {
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
         
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                // Récupération des modules controller et menu de l'utilisateur connecté
                $profiles =Profile_User::
                join('users','users.id','profile_user.user_id')->
                join('profiles',function($join){
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
              //->join('menus','menus.module_id','modules.id')
              //->join('sousmenus','sousmenus.menu_id','sousmenus.id')
              ->where('profile_user.user_id',Auth::user()->id)
              ->select('modules.*','controllers.id as controller_id',
              'fonctionnalites.id as fonctionnalite_id','fonctionnalites.nom as fonctionnalite_nom',
               'controllers.code as controller_code','controllers.nom as controller_nom',
               'controllers.icon as controller_icon'
               )
              ->get(); 

              $modules= array();
              $tab_module_user=array();
              $tab_fonction=array();
              $profiles_=$profiles;
              //contient de code des controllers auxquels l'utilisateur a droit
              $tab_ctrl_user=array();
              

            foreach($profiles as $profile){ 
                    $tab_controllers=array();       
                    //Rechercher tous les controllers du module         
                foreach($profiles_ as $prof){
                    // si le controller n'est pas encore ajouté  
                    if(!array_key_exists($prof->controller_id,$tab_controllers)){
                        if($prof->id==$profile->id)
                        $tab_controllers[$prof->controller_id]=$prof;
                    }
                }

                // définition des controllers d'un module
                if(!array_key_exists($profile->id,$modules)){
                    $tab_module_user[$profile->id]= $tab_controllers; 
                }

                // Si le module n'est pas ajouté, on l'ajoute
                if(!array_key_exists($profile->id,$modules)){
                    $modules[$profile->id]=$profile;
                }

                // Si la fonctionnalité n'est pas ajouté, on l'ajoute
                 if(!array_key_exists($profile->fonctionnalite_id,$tab_fonction)){
                    $tab_fonction[$profile->fonctionnalite_id]=$profile;
                }

                // Si le code n'est pas ajouté, on l'ajoute
                if( ! in_array($profile->controller_code,$tab_ctrl_user)){
                   array_push($tab_ctrl_user,$profile->controller_code);
                }
    
            }
            // Sélection des sousmenus
            $sousmenus = Sousmenu::join('menus','menus.id','sousmenus.menu_id')->
            whereNotIn('sousmenus.nom', $tab_ctrl_user)->
            select('sousmenus.*','menus.nom as menu_nom','menus.url as menu_url','menus.module_id')
            ->get();

              $request->session()->put('sousmenus',$sousmenus);      
              $request->session()->put('modules',$modules);
              $request->session()->put('profiles',$profiles);
              $request->session()->put('tab_module_user',$tab_module_user);
              
              return redirect('/dashboard');

            }
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        
           // return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
        }
        
}
