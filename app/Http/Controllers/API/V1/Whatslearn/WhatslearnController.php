<?php

namespace App\Http\Controllers\API\V1\Whatslearn;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Whatslearn\Whatslearn;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class WhatslearnController extends BaseController
{
    protected $whatslearn = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Whatslearn $whatslearn)
    {
        $this->middleware('auth:api');
        $this->whatslearn = $whatslearn;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $whatslearns = Whatslearn::orderBy('whatslearns.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(30, array('whatslearns.*'))
                            //    ->get()
                                ;


        return $this->sendResponse($whatslearns, 'Whatslearn list');
    }
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {

        $whatslearns = $this->whatslearn->get(['name', 'id']);
        return $this->sendResponse($whatslearns, 'Whatslearn list');
       
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
        $whatslearn= new Whatslearn();
        $whatslearn->name=$request->get('name');
        $whatslearn->icon = $request->get("icon");
        $whatslearn->status = $request->get("status");
        $whatslearn->save();
        return $this->sendResponse($whatslearn, 'Whatslearn Created Successfully');
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
    
        $whatslearn = $this->whatslearn->findOrFail($id);
        $whatslearn->update($request->all());


        $log= new Log();
        $log->action='Modification Whatslearn';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($whatslearn, 'Whatslearn Inwhatslearn has been updated');
    }


    public function destroy($id)
    {
        $whatslearn = $this->whatslearn->findOrFail($id);
        if( $whatslearn->status){
            $whatslearn->status=false;
        }else{
            $whatslearn->status=true;
        }
  
        $whatslearn->save();
        $log= new Log();
        $log->action="Suppression de Whatslearn";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($whatslearn, 'Whatslearn has been Deleted');
    }
}
