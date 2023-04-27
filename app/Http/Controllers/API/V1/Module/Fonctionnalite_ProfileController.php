<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Fonctionnalite_Profile;
use App\Models\Module\Profile;
use App\Models\Module\Fonctionnalite;
use App\Models\Module\Log;
use Illuminate\Http\Request;

class Fonctionnalite_ProfileController extends BaseController
{
    protected $fonctionnalite_profile = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Fonctionnalite_Profile $fonctionnalite_profile)
    {
        $this->middleware('auth:api');
        $this->fonctionnalite_profile = $fonctionnalite_profile;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $fonctionnalite_profiles = Profile::crossJoin('fonctionnalites')->get();
    return $this->sendResponse($fonctionnalite_profiles, 'Fonctionnalite_Profile list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {

        if( $request->get('profile_id')  && ! empty($request->get('profile_id'))){
            $profile_id=$request->get('profile_id');
        $fonctionnalite_profiles = Profile::crossJoin('fonctionnalites')
        ->join('controllers','controllers.id','=','fonctionnalites.controller_id')
        ->join('modules','modules.id','=','controllers.module_id')
        ->where('fonctionnalites.actif','=',true)
        ->where('profiles.actif','=',true)
        ->where('profiles.id',$profile_id)
        ->orderBy('profiles.nom', 'ASC')
        ->select(
        'profiles.id as profile_id', 
        'profiles.nom as profile_nom',
        'fonctionnalites.id as fonctionnalite_id', 
        'fonctionnalites.nom as fonctionnalite_nom',
        'modules.nom as module_nom', 
        'controllers.nom as controller_nom')
        ->addSelect(['droit' => Fonctionnalite_Profile::select('id as droit')
        ->whereColumn('profile_id', 'profiles.id')
        ->whereColumn('fonctionnalite_id', 'fonctionnalites.id')
        ->where('actif', true)
        ->latest()
        ->take(1)
            ])
        ->get();
        return $this->sendResponse($fonctionnalite_profiles, 'Fonctionnalite_Profile list');
        }else{
            $fonctionnalite_profiles = Profile::crossJoin('fonctionnalites')
            ->join('controllers','controllers.id','=','fonctionnalites.controller_id')
            ->join('modules','modules.id','=','controllers.module_id')
            ->where('fonctionnalites.actif','=',true)
            ->where('profiles.actif','=',true)
            ->orderBy('profiles.nom', 'ASC')
            ->select(
                'profiles.id as profile_id', 
                'profiles.nom as profile_nom',
                'fonctionnalites.id as fonctionnalite_id', 
                'fonctionnalites.nom as fonctionnalite_nom',
                'modules.nom as module_nom', 
                'controllers.nom as controller_nom')
                ->addSelect(['droit' => Fonctionnalite_Profile::select('id as droit')
                ->whereColumn('profile_id', 'profiles.id')
                ->whereColumn('fonctionnalite_id', 'fonctionnalites.id')
                ->where('actif', true)
                ->latest()
                ->take(1)
                    ])
            ->get();
            return $this->sendResponse($fonctionnalite_profiles, 'Fonctionnalites_X_Profiles list');
        }
    }

    public function list_off_droit(Request $request)
    {
        if( $request->get('profile_id')  && ! empty($request->get('profile_id'))){
            $profile_id=$request->get('profile_id');
            $fonctionnalite_profiles = $this->fonctionnalite_profile
            ->where('actif','=',true)
            ->where('profileid',$profile_id)
            ->get();
        }else{
            $fonctionnalite_profiles = $this->fonctionnalite_profile
            ->where('actif','=',true)
            ->get();
        }

     return $this->sendResponse($fonctionnalite_profiles, 'Fonctionnalite_Profile list');
    }





    /**
     * Store a newly created resource in storage.
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {


        $this->validate($request, [
            'nom' => 'required',
            'code_icao' => 'required',
            'devise' => 'required',
         //   'url_drapeau' => 'required',
            'actif' => 'required',
        ]);
   

        $url_drapeau="default_flag.jpg";
        if($request->url_drapeau){
            $name = time().'.' . explode('/', explode(':', substr($request->url_drapeau, 0, strpos($request->url_drapeau, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_drapeau=$request->get('code_icao').".". $extension;
            \Image::make($request->url_drapeau)->save(public_path('images/module/flag/'). $url_drapeau);          
        }
        $fonctionnalite_profile= new Fonctionnalite_Profile();
        $fonctionnalite_profile->nom=$request->get('nom');
        $fonctionnalite_profile->code_icao=$request->get('code_icao');
        $fonctionnalite_profile->devise=$request->get('devise');
        $fonctionnalite_profile->url_drapeau=  "/images/module/flag/".$url_drapeau;
        $fonctionnalite_profile->actif=$request->get('actif');
        $fonctionnalite_profile->save();
        

        return $this->sendResponse($fonctionnalite_profile, 'Fonctionnalite_Profile Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    
    {
        $this->validate($request, [
            'nom' => 'required',
            'code_icao' => 'required',
            'devise' => 'required',
            'actif' => 'required',
        ]);
        $fonctionnalite_profile = $this->fonctionnalite_profile->findOrFail($id);
        $url_drapeau="default_flag.jpg";
        if($request->url_drapeau){
            $tab= explode(':', $request->url_drapeau);
            if(sizeof( $tab)>1){
                $name = time().'.' . explode('/', explode(':', substr($request->url_drapeau, 0, strpos($request->url_drapeau, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_drapeau=$request->get('code_icao').".". $extension;
                \Image::make($request->url_drapeau)->save(public_path('images/module/flag/'). $url_drapeau);          
                $url_drapeau=  "/images/module/flag/".$url_drapeau;
                $request->offsetSet('url_drapeau', $url_drapeau);
            }
        }
        $fonctionnalite_profile->update($request->all());
        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($fonctionnalite_profile, 'Fonctionnalite_Profile Information has been updated');
    }

    public function change(Request $request){
        $this->validate($request, [
            'profile_id' => 'required',
            'fonctionnalite_id' => 'required'
        ]);
        $fonctionnalite_profile = $this->fonctionnalite_profile
        ->where('profile_id', $request->get('profile_id'))
        ->where('fonctionnalite_id', $request->get('fonctionnalite_id'))
        ->first();
        if (is_object($fonctionnalite_profile)){
            $fonctionnalite_profile->actif= ! $fonctionnalite_profile->actif;
        }else{
            $fonctionnalite_profile= new Fonctionnalite_Profile();
            $fonctionnalite_profile->fonctionnalite_id= $request->get('fonctionnalite_id');
            $fonctionnalite_profile->profile_id= $request->get('profile_id');
            $fonctionnalite_profile->actif=true;
        }
        $fonctionnalite_profile->save();

        return $this->sendResponse($fonctionnalite_profile, 'Fonctionnalite_Profile Updated Successfully');
        
    }
}
