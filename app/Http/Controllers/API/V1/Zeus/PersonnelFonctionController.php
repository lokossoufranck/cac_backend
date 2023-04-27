<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\PersonnelFonction;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PersonnelFonctionController extends BaseController
{
    protected $fonction = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PersonnelFonction $fonction)
    {
        $this->middleware('auth:api');
        $this->fonction = $fonction;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $fonctions = PersonnelFonction::orderBy('personnel_fonctions.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($fonctions, 'Personnel Fonction list');
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
        $fonction= new PersonnelFonction();
        $fonction->libelle=$request->get('libelle');
        $fonction->save();
        return $this->sendResponse($fonction, 'Personnel Fonction Created Successfully');
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
    
        $fonction = $this->fonction->findOrFail($id);
        // $fonction->update($request->all());

        $fonction->libelle=$request->get('libelle');
        $fonction->update();

        $log= new Log();
        $log->action='Modification Personnel Fonction';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($fonction, 'Personnel Fonction Information has been updated');
    }


    public function destroy($id)
    {
        $fonction = $this->fonction->findOrFail($id);
        $fonction->delete();

        $log= new Log();
        $log->action="Suppression de Personnel Fonction";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($fonction, 'Personnel Fonction has been Deleted');
    }
}
