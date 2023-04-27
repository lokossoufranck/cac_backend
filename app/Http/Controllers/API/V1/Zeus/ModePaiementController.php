<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\ModePaiement;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ModePaiementController extends BaseController
{
    protected $modePaiement = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModePaiement $modePaiement)
    {
        $this->middleware('auth:api');
        $this->modePaiement = $modePaiement;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        // $modePaiements = ModePaiement::orderBy('mode_paiements.created_at','ASC')
        //                         // ->where('actif', '=', true)
        //                         // ->paginate(10, array('mode_paiements.*'))
        //                         ->get()
                                ;

        $modePaiements = ModePaiement::join('mode_calculs', 'mode_calculs.id', '=', 'mode_paiements.mode_calcul_id')
                                // ->paginate(10, array('mode_paiements.*', 'mode_calculs.libelle as mode_calcul_libelle'))
                                ->select('mode_paiements.*', 'mode_calculs.libelle as mode_calcul_libelle')
                                ->get()
        ;

        return $this->sendResponse($modePaiements, 'Mode de Paiement list');
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
        $modePaiement= new ModePaiement();
        $modePaiement->libelle=$request->get('libelle');
        $modePaiement->heure_presence = $request->get("heure_presence");
        $modePaiement->calcul_smic = $request->get("calcul_smic");
        $modePaiement->variable_prorata = $request->get("variable_prorata");
        $modePaiement->jour_ferie = $request->get("jour_ferie");
        $modePaiement->mode_calcul_id = $request->get("mode_calcul_id");
        $modePaiement->prime_anciennete = $request->get("prime_anciennete");
        $modePaiement->save();
        return $this->sendResponse($modePaiement, 'Mode de Paiement Created Successfully');
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
    
        $modePaiement = $this->modePaiement->findOrFail($id);
        $modePaiement->update($request->all());

        // $modePaiement->libelle=$request->get('libelle');
        // $modePaiement->update();

        $log= new Log();
        $log->action='Modification Mode de Paiement';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($modePaiement, 'Mode de Paiement Information has been updated');
    }


    public function destroy($id)
    {
        $modePaiement = $this->modePaiement->findOrFail($id);
        // $modePaiement->actif = false;
        // $modePaiement->save();

        $modePaiement->delete();

        $log= new Log();
        $log->action="Suppression de Mode de Paiement";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($modePaiement, 'Mode de Paiement has been Deleted');
    }
}
