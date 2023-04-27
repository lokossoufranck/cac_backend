<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\ModeCalcul;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ModeCalculController extends BaseController
{
    protected $modeCalcul = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModeCalcul $modeCalcul)
    {
        $this->middleware('auth:api');
        $this->modeCalcul = $modeCalcul;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $modeCalculs = ModeCalcul::orderBy('mode_calculs.created_at','ASC')
                                // ->where('actif', '=', true)
                                // ->paginate(10, array('mode_calculs.*'))
                                ->get()
                                ;

        return $this->sendResponse($modeCalculs, 'Mode de Calcul list');
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
        $modeCalcul= new ModeCalcul();
        $modeCalcul->libelle=$request->get('libelle');
        $modeCalcul->save();
        return $this->sendResponse($modeCalcul, 'Mode de Calcul Created Successfully');
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
    
        $modeCalcul = $this->modeCalcul->findOrFail($id);
        // $modeCalcul->update($request->all());

        $modeCalcul->libelle=$request->get('libelle');
        $modeCalcul->actif=$request->get('actif');

        $modeCalcul->update();

        $log= new Log();
        $log->action='Modification Mode de Calcul';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($modeCalcul, 'Mode de Calcul Information has been updated');
    }


    public function destroy($id)
    {
        $modeCalcul = $this->modeCalcul->findOrFail($id);
        $modeCalcul->actif = false;
        $modeCalcul->save();

        // $modeCalcul->delete();

        $log= new Log();
        $log->action="Suppression de Mode de Calcul";
        $log->enregistrement=$modeCalcul->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($modeCalcul, 'Mode de Calcul has been Deleted');
    }
}
