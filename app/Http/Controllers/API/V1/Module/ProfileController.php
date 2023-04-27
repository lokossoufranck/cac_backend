<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\ProfileRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Profile;
use App\Models\Module\Site;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    protected $profile = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Profile $profile)
    {
        $this->middleware('auth:api');
        $this->profile = $profile;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //  $profile_s = $this->profile->orderBy('nom', 'ASC')->latest()->paginate(100);
        $profiles = Profile::join('sites', 'sites.id', '=', 'profiles.site_id')
        ->orderBy('sites.nom','ASC')
        ->paginate(10, array('profiles.*', 'sites.nom as sit_nom'));
        return $this->sendResponse($profiles, 'Profile list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        if( $request->get('site_id')  && ! empty($request->get('site_id'))){
            $site_id=$request->get('site_id');
            $profiles = $this->profile->where('site_id',$site_id)
            ->pluck('nom', 'id');
        }else{
            $profiles = $this->profile->pluck('nom', 'id');
        }    
        return $this->sendResponse($profiles, 'Profile list');
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
    public function store(ProfileRequest $request)
    {


       /* $this->validate($request, [
            'nom' => 'required|unique:profiles',
            'actif' => 'required',
        ]);*/
       
       
        $profile= new Profile();
        $profile->nom=$request->get('nom');
        $profile->site_id=$request->get('site_id');  
        $profile->actif=$request->get('actif');
        $profile->save();
        

        return $this->sendResponse($profile, 'Profile Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(ProfileRequest $request, $id)
    
    {
    
        $profile = $this->profile->findOrFail($id);
      
        $profile->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($profile, 'Profile Information has been updated');
    }


    public function destroy($id)
    {
        $profile = $this->profile->findOrFail($id);
        $profile->delete();

        $log= new Log();
        $log->action="Suppression de profile";
        $log->enregistrement=$profile->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($profile, 'profile has been Deleted');
    }
}
