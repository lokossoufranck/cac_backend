<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\PorteRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;

use App\Models\Module\Log;
use App\Models\Module\Porte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PorteController extends BaseController
{
    protected $porte = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Porte $porte)
    {
        $this->middleware('auth:api');
        $this->porte = $porte;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $portes = Porte::orderBy('nom','ASC')
        ->paginate(10, array('portes.*', 'nom'));
        return $this->sendResponse($portes, 'Porte list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
       $portes = $this->porte->pluck('nom', 'id');   
        return $this->sendResponse($portes, 'Porte list');
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
    public function store(PorteRequest $request)
    {


       /* $this->validate($request, [
            'nom' => 'required|unique:portes',
            'actif' => 'required',
        ]);*/
       
       
        $porte= new Porte();
        $porte->nom=$request->get('nom');
        $porte->save();
        

        return $this->sendResponse($porte, 'Porte Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(PorteRequest $request, $id)
    
    {
    
        $porte = $this->porte->findOrFail($id);
      
        $porte->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($porte, 'Porte Information has been updated');
    }


    public function destroy($id)
    {
        $porte = $this->porte->findOrFail($id);
        $porte->delete();

        $log= new Log();
        $log->action="Suppression de porte";
        $log->enregistrement=$porte->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($porte, 'porte has been Deleted');
    }
}
