<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\Modulef_WhatslearnRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Modulef_Whatslearn;
use App\Models\User;
use App\Models\Module\Site;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Modulef_WhatslearnController extends BaseController
{
    protected $modulef_whatslearn = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Modulef_Whatslearn $modulef_whatslearn)
    {
        $this->middleware('auth:api');
        $this->modulef_whatslearn = $modulef_whatslearn;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modulef_whatslearns = Modulef_Whatslearn::join('whatslearns', 'whatslearns.id', '=', 'modulef_whatslearn.whatslearn_id')
        ->join('modulefs', 'modulefs.id', '=', 'modulef_whatslearn.modulef_id')
        ->orderBy('whatslearns.id','DESC')
        ->paginate(10, array('modulef_whatslearn.*', 'whatslearns.name as whatslearn_nom','whatslearns.email as whatslearn_email',
        'modulefs.nom as modulef_nom','modulefs.site_id'));
        return $this->sendResponse($modulef_whatslearns, 'Modulef_Whatslearn list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        if( $request->get('modulef_id')  && ! empty($request->get('modulef_id'))){
            $modulef_id=$request->get('modulef_id');
            $modulef_whatslearns = Modulef_Whatslearn::join('whatslearns', 'whatslearns.id', '=', 'modulef_whatslearn.whatslearn_id')
            ->join('modulefs', 'modulefs.id', '=', 'modulef_whatslearn.modulef_id')
            ->where('modulef_whatslearn.modulef_id',$modulef_id)
            ->orderBy('whatslearns.id','DESC')
            ->paginate(10, array('modulef_whatslearn.*', 'whatslearns.name as whatslearn_nom','whatslearns.email as whatslearn_email',
            'modulefs.nom as modulef_nom','modulefs.site_id'));
           
        }else{
            $modulef_whatslearns = Modulef_Whatslearn::join('whatslearns', 'whatslearns.id', '=', 'modulef_whatslearn.whatslearn_id')
            ->join('modulefs', 'modulefs.id', '=', 'modulef_whatslearn.modulef_id')
            ->orderBy('whatslearns.id','DESC')
            ->paginate(10, array('modulef_whatslearn.*', 'whatslearns.name as whatslearn_nom','whatslearns.email as whatslearn_email',
            'modulefs.nom as modulef_nom','modulefs.site_id'));
        }
       
        return $this->sendResponse($modulef_whatslearns, 'Modulef_Whatslearn list');
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
    public function store(Modulef_WhatslearnRequest $request)
    {


        $modulef_whatslearn= new Modulef_Whatslearn();
        $modulef_whatslearn->whatslearn_id=$request->get('whatslearn_id');
        $modulef_whatslearn->modulef_id=$request->get('modulef_id'); 
        $modulef_whatslearn->site_id=$request->get('site_id');  
        $modulef_whatslearn->actif=$request->get('actif');
        $modulef_whatslearn->save();

        $id=$modulef_whatslearn->id;

        $log= new Log();
        $log->action='Enregistrement Modulef_Whatslearn';
        $log->enregistrement=$id;
        $log->whatslearn_id=\Auth::whatslearn()->id;
        $log->save();
        return $this->sendResponse($modulef_whatslearn, 'Modulef_Whatslearn Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Modulef_WhatslearnRequest $request, $id)
    
    {
    
        $modulef_whatslearn = $this->modulef_whatslearn->findOrFail($id);
        $modulef_whatslearn->update($request->all());

        $log= new Log();
        $log->action='Modification Modulef_Whatslearn';
        $log->enregistrement=$id;
        $log->whatslearn_id=\Auth::whatslearn()->id;
        $log->save();

        return $this->sendResponse($modulef_whatslearn, 'Modulef_Whatslearn Information has been updated');
    }


    public function destroy($id)
    {
        $modulef_whatslearn = $this->modulef_whatslearn->findOrFail($id);
        $modulef_whatslearn->delete();

        $log= new Log();
        $log->action="Suppression de modulef_whatslearn";
        $log->enregistrement=$modulef_whatslearn->id;
        $log->whatslearn_id=\Auth::whatslearn()->id;
        $log->save();
        return $this->sendResponse($modulef_whatslearn, 'Modulef_Whatslearn has been Deleted');
    }
}
