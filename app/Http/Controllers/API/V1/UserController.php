<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Users\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password; 



class UserController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['store','register','signin','reset','forgot']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('isAdmin')) {
            return $this->unauthorizedResponse();
        }

        $users = User::latest()->paginate(10);

        return $this->sendResponse($users, 'Users list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'firstname' => $request['firstname'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
        ]);
        event(new Registered($user));
        $token = ['token'=>$user->createToken('Laravel Password Grant Client')->accessToken];
        return $this->sendResponse($user, $token, 'User Created Successfully');
    }
    /******************** 
     * Register function
     * ***************** */

    public function register(UserRequest $request){
        $user= User::where('email',$request['email'])->first();
        if(!$user){ // User not exit in database
            $username=explode("@",$request['email'])[0];
            $user = User::create([
                'name' => $request['name'],
                'firstname' => $request['firstname'],
                'username' => $username,
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'type' => $request['type'],
            ]);  
            // send mail for user 
            $msg=" Succès ! Votre compte a été créé avec succès, un mail de confirmation a été envoyé à votre adresse e-mail
            veuillez vous connecter à votre compte mail pour valider votre inscription";
            $mail= $user->sendEmailVerificationNotification();
            $accessToken = ['accessToken'=>$user->createToken('Laravel Password Grant Client')->accessToken,
            'exist'=>false,
            'msg'=>$msg];
            $exist=['exist'=>false];
           // return URL::signedRoute('version', ['user' => 19]);
            return $this->sendResponse($user, $accessToken,$exist,'User Created Successfully');
        }else{
            $msg="Erreur ! Cette adresse e-mail est dejà lié à un compte !";        
            $exist=['exist'=>true,'msg'=>$msg];
            return $this->sendResponse($user,$exist,'User Exist');
        }     
    }

  /*******************
     *  Logout function
     ******************/

     function logout(){
        auth()->user()->tokens()->delete();
        return [
            'success'=>true,
            'message' => 'user logged out'
        ];

     }

    /*******************
     *  profil function
     ******************/

    function profil(){
        $user=auth('api')->user();
        return $this->sendResponse($user,'Connected user');

     }

    /*******************
     *  Signin function
     ******************/
    public function signin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);
        if ($validator->fails())
        {
           // return response(['errors'=>$validator->errors()->all()], 422);
            return $this->sendResponse(['errors'=>$validator->errors()->all()],422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $exist=true;
            if (Hash::check($request->password, $user->password)) {
                if($user->hasVerifiedEmail()){ // Check if email is verified
                    $accessToken = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $success=true;
                    $response = [
                    "accessToken"=> $accessToken,
                    "user"=>$user,
                    "exist"=> $exist,
                    "success"=>$success];

                    return response($response, 200);
                }else{
                    $msg="Votre compte existe mais n'est pas valide, un mail de confirmation a été envoyé à votre adresse e-mail
                     veuillez vous connecter à votre compte mail pour valider votre inscription";
                    $mail= $user->sendEmailVerificationNotification();
                    $success=false;
                    $response = ["msg" =>  $msg,"success"=>$success,"exist"=> $exist];
                    return response($response, 422);
                }
               
            } else {
                $success=false;
                $response = ["msg" => "Mot de passe incorrect",
                "exist"=> $exist,
                "success"=>$success];
                return response($response, 422);
            }
        } else {
            $success=false;
            $exist=false;
            $response = ["msg" =>"Il est possible que vous n'ayez pas encore de compte","success"=>$success, "exist"=> $exist];
            return response($response, 422);
        }
         
    }


    /**
     * Update the resource in storage
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());

        return $this->sendResponse($user, 'User Information has been updated');
    }

    /****
     * update_profil function
     */

    public function update_profil(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }
        $user->update($request->all());
        
        $response = [
            "user"=>$user,
            "message"=>"Mise à jour effectée avec succès !"
        ];
        return response($response, 200);
    }
 /***************
  *  forgot password
  *************/
    public function forgot() {
        $credentials = request()->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        //return response()->json(["msg" => 'Reset password link sent on your email id.']);
        $response = [
            "success"=>true,
            "message"=>"Un lien de réinitialisation a été envoyé à l'adresse indiquée !"
        ];
        return response($response, 200);
    }
/****************
 * reset password
 * ********** */
public function reset() {
    $credentials = request()->validate([
        'email' => 'required|email',
        'token' => 'required|string',
        'password' => 'required|string|confirmed'
    ]);

    $reset_password_status = Password::reset($credentials, function ($user, $password) {
        $user->password =  Hash::make($password) ;
        $user->save();
    });

    if ($reset_password_status == Password::INVALID_TOKEN) {
        return response()->json(["msg" => "Invalid token provided"], 400);
    }

    return response()->json(["msg" => "Le mot de passe a été modifié avec succès !"]);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $user = User::findOrFail($id);
        // delete the user

        $user->delete();

        return $this->sendResponse([$user], 'User has been Deleted');
    }
}