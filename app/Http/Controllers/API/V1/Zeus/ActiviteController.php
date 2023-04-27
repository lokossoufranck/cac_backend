<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Activite;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ActiviteController extends BaseController
{
    protected $activite = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Activite $activite)
    {
        $this->middleware('auth:api');
        $this->activite = $activite;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $activites = Activite::orderBy('activites.created_at','ASC')
                                ->where('actif', '=', true)
                                ->select('activites.*')
                                ->get()
                                // ->paginate(10, array('activites.*'))
                                ;
        return $this->sendResponse($activites, 'Activite list');
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
        $activite= new Activite();
        $activite->nom=$request->get('nom');
        $activite->save();
        return $this->sendResponse($activite, 'Activite Created Successfully');
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
    
        $activite = $this->activite->findOrFail($id);
        // $activite->update($request->all());

        $activite->nom=$request->get('nom');
        $activite->update();

        $log= new Log();
        $log->action='Modification Activite';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($activite, 'Activite Information has been updated');
    }


    public function destroy($id)
    {
        $activite = $this->activite->findOrFail($id);
        $activite->actif = false;
        $activite->save();

        // $activite->delete();

        $log= new Log();
        $log->action="Suppression de activite";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($activite, 'Activite has been Deleted');
    }
}
