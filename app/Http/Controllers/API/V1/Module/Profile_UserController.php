<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\Profile_UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Profile_User;
use App\Models\User;
use App\Models\Module\Site;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Profile_UserController extends BaseController
{
    protected $profile_user = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Profile_User $profile_user)
    {
        $this->middleware('auth:api');
        $this->profile_user = $profile_user;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profile_users = Profile_User::join('users', 'users.id', '=', 'profile_user.user_id')
        ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
        ->orderBy('users.id','DESC')
        ->paginate(10, array('profile_user.*', 'users.name as user_nom','users.email as user_email',
        'profiles.nom as profile_nom','profiles.site_id'));
        return $this->sendResponse($profile_users, 'Profile_User list');
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
            $profile_users = Profile_User::join('users', 'users.id', '=', 'profile_user.user_id')
            ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
            ->where('profile_user.profile_id',$profile_id)
            ->orderBy('users.id','DESC')
            ->paginate(10, array('profile_user.*', 'users.name as user_nom','users.email as user_email',
            'profiles.nom as profile_nom','profiles.site_id'));
           
        }else{
            $profile_users = Profile_User::join('users', 'users.id', '=', 'profile_user.user_id')
            ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
            ->orderBy('users.id','DESC')
            ->paginate(10, array('profile_user.*', 'users.name as user_nom','users.email as user_email',
            'profiles.nom as profile_nom','profiles.site_id'));
        }
       
        return $this->sendResponse($profile_users, 'Profile_User list');
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
    public function store(Profile_UserRequest $request)
    {


        $profile_user= new Profile_User();
        $profile_user->user_id=$request->get('user_id');
        $profile_user->profile_id=$request->get('profile_id'); 
        $profile_user->site_id=$request->get('site_id');  
        $profile_user->actif=$request->get('actif');
        $profile_user->save();

        $id=$profile_user->id;

        $log= new Log();
        $log->action='Enregistrement Profile_User';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($profile_user, 'Profile_User Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Profile_UserRequest $request, $id)
    
    {
    
        $profile_user = $this->profile_user->findOrFail($id);
        $profile_user->update($request->all());

        $log= new Log();
        $log->action='Modification Profile_User';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($profile_user, 'Profile_User Information has been updated');
    }


    public function destroy($id)
    {
        $profile_user = $this->profile_user->findOrFail($id);
        $profile_user->delete();

        $log= new Log();
        $log->action="Suppression de profile_user";
        $log->enregistrement=$profile_user->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($profile_user, 'Profile_User has been Deleted');
    }
}
