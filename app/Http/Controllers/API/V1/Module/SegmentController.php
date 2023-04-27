<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\SegmentRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Segment;
use App\Models\Module\Campagne;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SegmentController extends BaseController
{
    protected $segment = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Segment $segment)
    {
        $this->middleware('auth:api');
        $this->segment = $segment;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $segments = Segment::join('campagnes', 'campagnes.id', '=', 'segments.campagne_id')
        ->orderBy('campagnes.nom','ASC')
        ->paginate(10, array('segments.*', 'campagnes.nom as camp_nom'));
        return $this->sendResponse($segments, 'Segment list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request,$campagne_id)
    {


        if(  isset($campagne_id)  && ! empty($campagne_id)){
          
            $segments = $this->segment->where('campagne_id',$campagne_id)
            ->pluck('nom', 'id');
        }else{
            $segments = $this->segment->pluck('nom', 'id');
        }    
        return $this->sendResponse($segments, 'Segment list');
    }

    public function show(){
        
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
    public function store(SegmentRequest $request)
    {


       /* $this->validate($request, [
            'nom' => 'required|unique:segments',
            'actif' => 'required',
        ]);*/
       
       
        $segment= new Segment();
        $segment->nom=$request->get('nom');
        $segment->description=$request->get('description');
        $segment->campagne_id=$request->get('campagne_id');  
        $segment->actif=$request->get('actif');
        $segment->save();
        

        return $this->sendResponse($segment, 'Segment Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(SegmentRequest $request, $id)
    
    {
    
        $segment = $this->segment->findOrFail($id);
      
        $segment->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($segment, 'Segment Information has been updated');
    }


    public function destroy($id)
    {
        $segment = $this->segment->findOrFail($id);
        $segment->delete();

        $log= new Log();
        $log->action="Suppression de segment";
        $log->enregistrement=$segment->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($segment, 'segment has been Deleted');
    }
}
