<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Conge;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CongeController extends BaseController
{
    protected $conge = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Conge $conge)
    {
        $this->middleware('auth:api');
        $this->conge = $conge;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $conges = Conge::orderBy('conges.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($conges, 'Conge list');
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
        $conge= new Conge();
        $conge->date_demande=$request->get('date_demande');
        $conge->date_debut=$request->get('date_debut');
        $conge->date_fin=$request->get('date_fin');
        $conge->date_reprise=$request->get('date_reprise');
        $conge->nombre_jour=$request->get('nombre_jour');
        $conge->conge_categorie_id=$request->get('conge_categorie_id');
        $conge->personnel_id=$request->get('personnel_id');

        $conge->save();
        return $this->sendResponse($conge, 'Conge Created Successfully');
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
    
        $conge = $this->conge->findOrFail($id);
        $conge->update($request->all());

        // $conge->libelle=$request->get('libelle');
        $conge->update();

        $log= new Log();
        $log->action='Modification Conge';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($conge, 'Conge Information has been updated');
    }


    public function destroy($id)
    {
        $conge = $this->conge->findOrFail($id);
        $conge->delete();

        $log= new Log();
        $log->action="Suppression de Conge";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($conge, 'Conge has been Deleted');
    }
}
