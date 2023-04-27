<?php

namespace App\Http\Controllers\API\V1\Module;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Service;
use App\Models\Module\Log;

class ServiceController extends BaseController
{
    protected $service = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Service $service)
    {
        $this->middleware('auth:api');
        $this->service = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = $this->service->orderBy('nom', 'ASC')->latest()->paginate(100);
        // $deparements = Service::join('site', 'site.id', '=', 'services.site_id')
        // ->paginate(10, array('services.*', 'site.nom as site_nom'));
        return $this->sendResponse($services, 'Service list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $service = $this->service->pluck('nom', 'id')->where('actif','=',true);
        return $this->sendResponse($service, 'Service list');
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
            'departement_id'    =>  'required'
        ]);

        $service = new Service();
        $service->nom=$request->get('nom');
        $service->departement_id=$request->get('departement_id');
        $service->save();
        

        return $this->sendResponse($service, 'Service Created Successfully');
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

        $service = $this->service->findOrFail($id);
        $service->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($service, 'Service Information has been updated');
    }

    public function destroy($id)
    {
        $service = $this->service->findOrFail($id);

        $service->actif = false;
        $service->save();

        // $service->delete();

        $log= new Log();
        $log->action="Suppression de service";
        $log->enregistrement=$service->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($service, 'Service has been Deleted');
    }
}
