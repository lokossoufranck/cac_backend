<?php
namespace App\Http\Controllers\API\V1\Module;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Module;
use App\Models\Module\Sousmenu;
use App\Models\Module\Menu;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class SousmenuController extends BaseController
{
    protected $sousmenu = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Sousmenu $sousmenu)
    {
        $this->middleware('auth:api');
        $this->sousmenu = $sousmenu;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $sousmenus = $this->sousmenu->latest()->paginate(10);
        $sousmenus = Sousmenu::join('menus', 'menus.id', '=', 'sousmenus.menu_id')
            ->paginate(10, array('sousmenus.*', 'menus.nom as men_nom'));
        return $this->sendResponse($sousmenus, 'Sousmenu list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $sousmenus = $this->module->pluck('nom', 'id');
        return $this->sendResponse($sousmenus, 'Sousmenu list');
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
        $sousmenu = $this->sousmenu->create([
            'nom' => $request->get('nom'),
            'icon' => $request->get('icon'),
            'url' => $request->get('url'),
            'menu_id' => $request->get('menu_id'),
            'actif' => $request->get('actif')
        ]);


        $log= new Log();
        $log->action='Modification Sousmenu';
        $log->enregistrement=$sousmenu->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($sousmenu,'Sousmenu Created Successfully');
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
        $sousmenu = $this->sousmenu->findOrFail($id);

        $sousmenu->update($request->all());

        return $this->sendResponse($sousmenu, 'Sousmenu Information has been updated');
    }

    public function destroy($id)
    {


        $sousmenu = $this->sousmenu->findOrFail($id);
        $sousmenu->delete();

        $log= new Log();
        $log->action="Suppression de sousmenu";
        $log->enregistrement=$sousmenu->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($sousmenu, 'Sousmenu has been Deleted');
    }


  



  

}

    

