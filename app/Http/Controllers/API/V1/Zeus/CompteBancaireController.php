<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Banque;
use App\Models\Module\Log;
use App\Models\Zeus\CompteBancaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CompteBancaireController extends BaseController
{
    protected $compte = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CompteBancaire $compte)
    {
        $this->middleware('auth:api');
        $this->compte = $compte;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                   

        $personnel_id = $request->get("personnel_id");

        $query = CompteBancaire::join('banques', 'banques.id', '=', 'compte_bancaires.banque_id')
                    ->select('compte_bancaires.*', 'banques.nom as banque_nom')
                    ;

        if ($personnel_id){
            $query->where('compte_bancaires.personnel_id', '=', $personnel_id);
        }
        
        $comptes = $query->get();

        return $this->sendResponse($comptes, 'Compte bancaire list');
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
        $compte= new CompteBancaire();
        $compte->numero=$request->get('numero');
        $compte->banque_id=$request->get('banque_id');
        $compte->personnel_id=$request->get('personnel_id');

        $actif = $request->get("actif");

        if ($actif == true){
            $personnel_id = $request->get("personnel_id");
            $result = CompteBancaire::where([
                ["personnel_id", "=", $personnel_id],
            ])->update(["actif" => false]);
        }

        $compte->actif = $actif;

        $compte->save();
        return $this->sendResponse($compte, 'Compte Bancaire Created Successfully');
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
    
        $compte = $this->compte->findOrFail($id);
        $actif = $request->get("actif");

        if ($actif == true){
            $personnel_id = $request->get("personnel_id");
            $result = CompteBancaire::where([
                ["personnel_id", "=", $personnel_id],
            ])->update(["actif" => false]);
        }

        $compte->numero=$request->get('numero');
        $compte->actif = $actif;
        $compte->update();

        $log= new Log();
        $log->action='Modification Compte Bancaire';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($compte, 'Compte Bancaire Information has been updated');
    }


    public function destroy($id)
    {
        $compte = $this->compte->findOrFail($id);
        // $compte->actif = false;
        // $compte->save();

        $compte->delete();

        $log= new Log();
        $log->action="Suppression de Compte Bancaire";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($compte, 'Compte Bancaire has been Deleted');
    }
}
