<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Module;
use App\Models\Module\ControllerErp;
use App\Models\Module\Menu;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class ControllerController extends BaseController
{
    protected $controller = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ControllerErp $controller)
    {
        $this->middleware('auth:api');
        $this->controller = $controller;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $controllers = $this->controller->latest()->paginate(10);
        $controllers = ControllerErp::join('modules', 'modules.id', '=', 'controllers.module_id')
            ->paginate(10, array('controllers.*', 'modules.nom as mod_nom'));
        return $this->sendResponse($controllers, 'ControllerErp list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $controllers = $this->controller->pluck('nom', 'id');
        return $this->sendResponse($controllers, 'ControllerErp list');
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
            'code' => 'required',
            'module_id' => 'required',

        ]);
        $controller = $this->controller->create([
            'nom' => $request->get('nom'),
            'code' => $request->get('code'),
            'icon' => $request->get('icon'),
            'module_id' => $request->get('module_id'),
            'url' => $request->get('url'),
            'controller_id' => $request->get('controller_id'),
            'actif' => $request->get('actif')
        ]);
        $controller->save();

        $log= new Log();
        $log->action='Modification ControllerErp';
        $log->enregistrement=$controller->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($controller,'ControllerErp Created Successfully');
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
        $controller = $this->controller->findOrFail($id);

        $controller->update($request->all());

        return $this->sendResponse($controller, 'ControllerErp Information has been updated');
    }

    public function destroy($id)
    {


        $controller = $this->controller->findOrFail($id);
        $controller->delete();

        $log= new Log();
        $log->action="Suppression de controller";
        $log->enregistrement=$controller->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($controller, 'ControllerErp has been Deleted');
    }


  



  

}

    

