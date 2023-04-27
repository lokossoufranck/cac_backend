<?php

namespace App\Http\Controllers\API\V1\Module;

use Illuminate\Http\Request;
use App\Models\Module\Client;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Log;
// use App\Models\Module\Site;

class ClientController extends BaseController
{
    protected $client = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->middleware('auth:api');
        $this->client = $client;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::join('sites', 'sites.id', '=', 'clients.site_id')
                            ->select('clients.*', 'sites.nom as site_nom')
                            ->get()
                            // ->paginate(10, array('banques.*', 'sites.nom as site_nom'))
                            ;
        return $this->sendResponse($clients, 'Client list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $client = $this->client->pluck('nom', 'id');
        return $this->sendResponse($client, 'Client list');
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
            // 'site_id'   =>  'required'
        ]);

        $client = new Client();
        $client->nom=$request->get('nom');
        $client->site_id=$request->get('site_id');
        $client->save();
        

        return $this->sendResponse($client, 'Client Created Successfully');
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
        $this->validate($request, [
            'nom' => 'required',
        ]);

        $client = $this->client->findOrFail($id);
        $client->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($client, 'Client Information has been updated');
    }

    public function destroy($id)
    {
        $client = $this->client->findOrFail($id);
        $client->delete();

        $log= new Log();
        $log->action="Suppression de département";
        $log->enregistrement=$client->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($client, 'Client has been Deleted');
    }
}
