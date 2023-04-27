<?php

namespace App\Http\Controllers\API\V1\Formation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Formation\Formation;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class FormationController extends BaseController
{
    protected $formation = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Formation $formation)
    {
        $this->middleware('auth:api')->except(['index','list']);;
        $this->formation = $formation;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $formations = Formation::orderBy('formations.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(3, array('formations.*'))
                            //    ->get()
                                ;


        return $this->sendResponse($formations, 'Formation list');
    }


       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {

        $formations = $this->formation->get(['name', 'id']);
        return $this->sendResponse($formations, 'Formation list');
       
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
        $formation= new Formation();
        $formation->name=$request->get('name');
        $formation->code = $request->get("code");
        $formation->order = $request->get("order");
        $formation->status = $request->get("status");
        $formation->url_photo_1 = $request->get("url_photo_1");
        $formation->description_photo_1 = $request->get("description_photo_1");
        $formation->url_photo_2 = $request->get("url_photo_2");
        $formation->description_photo_2 = $request->get("description_photo_2");      
        $formation->lang = $request->get("lang");
        $formation->save();
        return $this->sendResponse($formation, 'Formation Created Successfully');
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
    
        $formation = $this->formation->findOrFail($id);
        $formation->update($request->all());


        $log= new Log();
        $log->action='Modification Formation';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($formation, 'Formation Information has been updated');
    }


    public function destroy($id)
    {
        $formation = $this->formation->findOrFail($id);
        if( $formation->status){
            $formation->status=false;
        }else{
            $formation->status=true;
        }
  
        $formation->save();
        $log= new Log();
        $log->action="Suppression de Formation";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($formation, 'Formation has been Deleted');
    }
}
