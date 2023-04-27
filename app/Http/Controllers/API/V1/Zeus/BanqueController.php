<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Banque;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class BanqueController extends BaseController
{
    protected $banque = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Banque $banque)
    {
        $this->middleware('auth:api');
        $this->banque = $banque;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                   ;

        $banques = Banque::join('sites', 'sites.id', '=', 'banques.site_id')
                            ->where('banques.actif', '=', true)
                            ->select('banques.*', 'sites.nom as site_nom')
                            ->get()
                            // ->paginate(10, array('banques.*', 'sites.nom as site_nom'))
                            ;

        return $this->sendResponse($banques, 'Banque list');
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
        $banque= new Banque();
        $banque->nom=$request->get('nom');
        $banque->numero_de_compte=$request->get('numero_de_compte');
        $banque->site_id=$request->get('site_id');
        $banque->save();
        return $this->sendResponse($banque, 'Banque Created Successfully');
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
    
        $banque = $this->banque->findOrFail($id);
        // $banque->update($request->all());

        $banque->nom=$request->get('nom');
        $banque->numero_de_compte=$request->get('numero_de_compte'); 
        $banque->site_id=$request->get('site_id');
        $banque->update();

        $log= new Log();
        $log->action='Modification Banque';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($banque, 'Banque Information has been updated');
    }


    public function destroy($id)
    {
        $banque = $this->banque->findOrFail($id);
        $banque->actif = false;
        $banque->save();

        // $banque->delete();

        $log= new Log();
        $log->action="Suppression de banque";
        $log->enregistrement=$banque->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($banque, 'Banque has been Deleted');
    }
}
