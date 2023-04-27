<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Module;
use App\Models\Module\Fonctionnalite;
use App\Models\Module\Menu;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class FonctionnaliteController extends BaseController
{
    protected $fonctionnalite = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Fonctionnalite $fonctionnalite)
    {
        $this->middleware('auth:api');
        $this->fonctionnalite = $fonctionnalite;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $fonctionnalites = $this->fonctionnalite->latest()->paginate(10);
        $fonctionnalites = Fonctionnalite::join('controllers', 'controllers.id', '=', 'fonctionnalites.controller_id')
            ->paginate(10, array('fonctionnalites.*', 'controllers.nom as ctrl_nom'));
        return $this->sendResponse($fonctionnalites, 'Fonctionnalite list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $fonctionnalites = $this->fonctionnalite->pluck('nom', 'id');
        return $this->sendResponse($fonctionnalites, 'Fonctionnalite list');
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

        ]);
        $fonctionnalite = $this->fonctionnalite->create([
            'nom' => $request->get('nom'),
            'url' => $request->get('url'),
            'controller_id' => $request->get('controller_id'),
            'actif' => $request->get('actif')
        ]);


        $log= new Log();
        $log->action='Modification Fonctionnalite';
        $log->enregistrement=$fonctionnalite->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($fonctionnalite,'Fonctionnalite Created Successfully');
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
        $fonctionnalite = $this->fonctionnalite->findOrFail($id);

        $fonctionnalite->update($request->all());

        return $this->sendResponse($fonctionnalite, 'Fonctionnalite Information has been updated');
    }

    public function destroy($id)
    {


        $fonctionnalite = $this->fonctionnalite->findOrFail($id);
        $fonctionnalite->delete();

        $log= new Log();
        $log->action="Suppression de fonctionnalite";
        $log->enregistrement=$fonctionnalite->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($fonctionnalite, 'Fonctionnalite has been Deleted');
    }


  



  

}

    

