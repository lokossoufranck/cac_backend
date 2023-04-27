<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Nationalite;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class NationaliteController extends BaseController
{
    protected $nationalite = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Nationalite $nationalite)
    {
        // $this->middleware('auth:api');
        $this->nationalite = $nationalite;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $nationalites = Nationalite::orderBy('nationalites.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($nationalites, 'Nationalite list');
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
        $nationalite= new Nationalite();
        $nationalite->libelle=$request->get('libelle');
        $nationalite->save();
        return $this->sendResponse($nationalite, 'Nationalite Created Successfully');
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
    
        $nationalite = $this->nationalite->findOrFail($id);
        // $nationalite->update($request->all());

        $nationalite->libelle=$request->get('libelle');
        $nationalite->update();

        $log= new Log();
        $log->action='Modification Nationalite';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($nationalite, 'Nationalite Information has been updated');
    }


    public function destroy($id)
    {
        $nationalite = $this->nationalite->findOrFail($id);
        $nationalite->delete();

        $log= new Log();
        $log->action="Suppression de Nationalite";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($nationalite, 'Nationalite has been Deleted');
    }
}
