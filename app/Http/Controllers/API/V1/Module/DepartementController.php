<?php

namespace App\Http\Controllers\API\V1\Module;

use Illuminate\Http\Request;
use App\Models\Module\Departement;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Log;
use App\Models\Module\Site;

class DepartementController extends BaseController
{
    protected $departement = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Departement $departement)
    {
        $this->middleware('auth:api');
        $this->departement = $departement;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $departements = $this->departement->orderBy('nom', 'ASC')->latest()->paginate(100);
        $departements = Departement::join('sites', 'sites.id', '=', 'departements.site_id')
        // ->where('actif', true)
        ->paginate(10, array('departements.*', 'sites.nom as site_nom'));
        return $this->sendResponse($departements, 'Departement list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $departement = $this->departement->pluck('nom', 'id');
        return $this->sendResponse($departement, 'Departement list');
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


        $this->validate($request, [
            'nom' => 'required',
            'site_id'   =>  'required'
        ]);

        $departement = new Departement();
        $departement->nom=$request->get('nom');
        $departement->site_id=$request->get('site_id');
        $departement->save();
        

        return $this->sendResponse($departement, 'Departement Created Successfully');
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
        $this->validate($request, [
            'nom' => 'required',
        ]);

        $departement = $this->departement->findOrFail($id);
        $departement->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($departement, 'Departement Information has been updated');
    }

    public function destroy($id)
    {
        $departement = $this->departement->findOrFail($id);
        $departement->actif = false;
        $departement->save();

        // $departement->delete();

        $log= new Log();
        $log->action="Suppression de département";
        $log->enregistrement=$departement->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($departement, 'Departement has been Deleted');
    }
}
