<?php

namespace App\Http\Controllers\API\V1\Event;
use App\Http\Requests\Event\DysfonctionnementRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Event\Dysfonctionnement;
use App\Models\Module\Campagne;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DysfonctionnementController extends BaseController
{
    protected $dysfonctionnement = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Dysfonctionnement $dysfonctionnement)
    {
        $this->middleware('auth:api');
        $this->dysfonctionnement = $dysfonctionnement;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $dysfonctionnements = Dysfonctionnement::join('campagnes', 'campagnes.id', '=', 'dysfonctionnements.campagne_id')
        ->orderBy('campagnes.nom','ASC')
        ->paginate(10, array('dysfonctionnements.*', 'campagnes.nom as camp_nom'));
        return $this->sendResponse($dysfonctionnements, 'Dysfonctionnement list');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request,$campagne_id,$porte_id)
    {

      
        if( isset($campagne_id) && isset($porte_id)&& !empty($campagne_id) && !empty($porte_id) ){        
            $dysfonctionnements = $this->dysfonctionnement
            ->where('campagne_id',$campagne_id)
            ->where('porte_id',$porte_id)
            ->pluck('nom', 'id');
        }else{
            $dysfonctionnements = $this->dysfonctionnement->pluck('nom', 'id');
        }    
        return $this->sendResponse($dysfonctionnements, 'Dysfonctionnement list');
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
    public function store(DysfonctionnementRequest $request)
    {


       /* $this->validate($request, [
            'nom' => 'required|unique:dysfonctionnements',
            'actif' => 'required',
        ]);*/
       
       
        $dysfonctionnement= new Dysfonctionnement();
        $dysfonctionnement->nom=$request->get('nom');
        $dysfonctionnement->description=$request->get('description');
        $dysfonctionnement->campagne_id=$request->get('campagne_id');  
        $dysfonctionnement->porte_id=$request->get('porte_id');  
        $dysfonctionnement->actif=$request->get('actif');
        $dysfonctionnement->save();
        

        return $this->sendResponse($dysfonctionnement, 'Dysfonctionnement Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(DysfonctionnementRequest $request, $id)
    
    {
    
        $dysfonctionnement = $this->dysfonctionnement->findOrFail($id);
      
        $dysfonctionnement->update($request->all());

        $log= new Log();
        $log->action='Modification Dysfonctionnement';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($dysfonctionnement, 'Dysfonctionnement Information has been updated');
    }


    public function destroy($id)
    {
        $dysfonctionnement = $this->dysfonctionnement->findOrFail($id);
        $dysfonctionnement->delete();

        $log= new Log();
        $log->action="Suppression de dysfonctionnement";
        $log->enregistrement=$dysfonctionnement->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($dysfonctionnement, 'dysfonctionnement has been Deleted');
    }
}
