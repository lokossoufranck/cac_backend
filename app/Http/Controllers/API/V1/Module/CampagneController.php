<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\CampagneRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Campagne;
use App\Models\Module\Site;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampagneController extends BaseController
{
    protected $campagne = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Campagne $campagne)
    {
        $this->middleware('auth:api');
        $this->campagne = $campagne;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $campagnes = Campagne::join('sites', 'sites.id', '=', 'campagnes.site_id')
        ->orderBy('sites.nom','ASC')
        ->paginate(10, array('campagnes.*', 'sites.nom as sit_nom'));
        return $this->sendResponse($campagnes, 'Campagne list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        if( $request->get('site_id')  && ! empty($request->get('site_id'))){
            $site_id=$request->get('site_id');
            $campagnes = $this->campagne->where('site_id',$site_id)
            ->pluck('nom', 'id');
        }else{
            $campagnes = $this->campagne->pluck('nom', 'id');
        }    
        return $this->sendResponse($campagnes, 'Campagne list');
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
    public function store(CampagneRequest $request)
    {


       /* $this->validate($request, [
            'nom' => 'required|unique:campagnes',
            'actif' => 'required',
        ]);*/
       
       
        $campagne= new Campagne();
        $campagne->nom=$request->get('nom');
        $campagne->description=$request->get('description');
        $campagne->site_id=$request->get('site_id');  
        $campagne->actif=$request->get('actif');
        $campagne->save();
        

        return $this->sendResponse($campagne, 'Campagne Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(CampagneRequest $request, $id)
    
    {
    
        $campagne = $this->campagne->findOrFail($id);
      
        $campagne->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($campagne, 'Campagne Information has been updated');
    }


    public function destroy($id)
    {
        $campagne = $this->campagne->findOrFail($id);
        $campagne->delete();

        $log= new Log();
        $log->action="Suppression de campagne";
        $log->enregistrement=$campagne->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($campagne, 'campagne has been Deleted');
    }
}
