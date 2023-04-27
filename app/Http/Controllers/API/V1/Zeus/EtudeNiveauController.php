<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\EtudeNiveau;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class EtudeNiveauController extends BaseController
{
    protected $niveau = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EtudeNiveau $niveau)
    {
        // $this->middleware('auth:api');
        $this->niveau = $niveau;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $niveaux = EtudeNiveau::orderBy('etude_niveaux.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($niveaux, 'Niveau Etude list');
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
        $niveau= new EtudeNiveau();
        $niveau->libelle=$request->get('libelle');
        $niveau->save();
        return $this->sendResponse($niveau, 'Niveau Etude Created Successfully');
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
    
        $niveau = $this->niveau->findOrFail($id);
        // $niveau->update($request->all());

        $niveau->libelle=$request->get('libelle');
        $niveau->update();

        $log= new Log();
        $log->action='Modification Niveau Etude';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($niveau, 'Niveau Etude Information has been updated');
    }


    public function destroy($id)
    {
        $niveau = $this->niveau->findOrFail($id);

        $niveau->delete();

        $log= new Log();
        $log->action="Suppression de Niveau Etude";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($niveau, 'Niveau Etude has been Deleted');
    }
}
