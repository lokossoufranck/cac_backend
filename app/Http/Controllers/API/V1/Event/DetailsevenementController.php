<?php

namespace App\Http\Controllers\API\V1\Event;
use App\Http\Requests\Event\DetailsevenementRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Event\Detailsevenement;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DetailsevenementController extends BaseController
{
    protected $detailsevenement = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Detailsevenement $detailsevenement)
    {
        $this->middleware('auth:api');
        $this->detailsevenement = $detailsevenement;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $detailsevenements = Detailsevenement::orderBy('detailsevenements.id','ASC')
        ->paginate(10, array('detailsevenements.*'));
        return $this->sendResponse($detailsevenements, 'Detailsevenement list');
    }
    


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request,$evenement_id)
    {

        if(  isset($evenement_id)  && ! empty($evenement_id)){
            
        $detailsevenements = Detailsevenement::join('segments','segments.id','detailsevenements.segment_id')
        ->join('dysfonctionnements','dysfonctionnements.id','detailsevenements.dysfonctionnement_id')
        -> where('evenement_id',$evenement_id)
        ->get(
            ['detailsevenements.*',
            'segments.nom AS segment_nom',
             'dysfonctionnements.nom AS dysfonctionnement_nom',
             'dysfonctionnements.porte_id',
             'dysfonctionnements.campagne_id',
             
             ]
            );
            

                   
        }else{
            $detailsevenements = Detailsevenement::where('evenement_id',$evenement_id)
            ->get(
                ['detailsevenements.*',
                'segments.nom AS segment_nom',
                 'dysfonctionnements.nom AS dysfonctionnement_nom']
            );
          
        }
         
        return $this->sendResponse($detailsevenements, 'Detailsevenement list');
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
    public function store(DetailsevenementRequest $request)
    {
      
        $detailsevenement= new Detailsevenement();
        $detailsevenement->segment_id=$request->get('segment_id');
        $detailsevenement->dysfonctionnement_id=$request->get('dysfonctionnement_id');  
        $detailsevenement->evenement_id=$request->get('evenement_id'); 
        $detailsevenement->volume_prevu=0;
        $detailsevenement->volume_realise=0;
        $detailsevenement->save();
        return $this->sendResponse($detailsevenement, 'Detailsevenement Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(DetailsevenementRequest $request, $id)
    
    {
    
        $detailsevenement = $this->detailsevenement->findOrFail($id);
      
        $detailsevenement->update($request->all());

        $log= new Log();
        $log->action='Modification Detailsevenement';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($detailsevenement, 'Detailsevenement Information has been updated');
    }


    public function destroy($id)
    {
        $detailsevenement = $this->detailsevenement->findOrFail($id);
        $detailsevenement->delete();

        $log= new Log();
        $log->action="Suppression de detailsevenement";
        $log->enregistrement=$detailsevenement->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($detailsevenement, 'detailsevenement has been Deleted');
    }
}
