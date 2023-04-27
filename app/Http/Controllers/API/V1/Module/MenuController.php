<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Module;
use App\Models\Module\Menu;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class MenuController extends BaseController
{
    protected $menu = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Menu $menu)
    {
        $this->middleware('auth:api');
        $this->menu = $menu;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $menus = $this->menu->latest()->paginate(10);
        $menus = Menu::join('modules', 'modules.id', '=', 'menus.module_id')
            ->paginate(10, array('menus.*', 'modules.nom as mod_nom'));
        return $this->sendResponse($menus, 'Menu list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $menus = $this->menu->pluck('nom', 'id');
        return $this->sendResponse($menus, 'Menu list');
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
        $menu = $this->menu->create([
            'nom' => $request->get('nom'),
            'icon' => $request->get('icon'),
            'url' => $request->get('url'),
            'module_id' => $request->get('module_id'),
            'actif' => $request->get('actif')
        ]);


        $log= new Log();
        $log->action='Modification Menu';
        $log->enregistrement=$menu->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($menu,'Menu Created Successfully');
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
        $menu = $this->menu->findOrFail($id);

        $menu->update($request->all());

        return $this->sendResponse($menu, 'Menu Information has been updated');
    }

    public function destroy($id)
    {


        $menu = $this->menu->findOrFail($id);
        $menu->delete();

        $log= new Log();
        $log->action="Suppression de menu";
        $log->enregistrement=$menu->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($menu, 'Menu has been Deleted');
    }


  



  

}

    

