<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Pays;
use App\Models\Module\Log;
use Illuminate\Http\Request;

class PaysController extends BaseController
{
    protected $pays = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Pays $pays)
    {
        $this->middleware('auth:api');
        $this->pays = $pays;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pays_s = $this->pays->orderBy('nom', 'ASC')->latest()->paginate(100);

        return $this->sendResponse($pays_s, 'Pays list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $pays_s = $this->pays->pluck('nom', 'id');
        return $this->sendResponse($pays_s, 'Pays list');
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
            'code_icao' => 'required',
            'devise' => 'required',
         //   'url_drapeau' => 'required',
            'actif' => 'required',
        ]);
       /* $pays = $this->pays->create([
            'nom' => $request->get('nom'),
            'code_icao' => $request->get('code_icao'),
            'devise' => $request->get('devise'),
            'url_drapeau' => $request->get('url_drapeau'),
            'actif' => $request->get('actif')
        ]);*/

        $url_drapeau="default_flag.jpg";
        if($request->url_drapeau){
            $name = time().'.' . explode('/', explode(':', substr($request->url_drapeau, 0, strpos($request->url_drapeau, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_drapeau=$request->get('code_icao').".". $extension;
            \Image::make($request->url_drapeau)->save(public_path('images/module/flag/'). $url_drapeau);          
        }

        $pays= new Pays();
        $pays->nom=$request->get('nom');
        $pays->code_icao=$request->get('code_icao');
        $pays->devise=$request->get('devise');
        $pays->url_drapeau=  "/images/module/flag/".$url_drapeau;
        $pays->actif=$request->get('actif');
        $pays->save();
        

        return $this->sendResponse($pays, 'Pays Created Successfully');
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
            'code_icao' => 'required',
            'devise' => 'required',
            'actif' => 'required',
        ]);
        
        $pays = $this->pays->findOrFail($id);
        $url_drapeau="default_flag.jpg";
        if($request->url_drapeau){
            $tab= explode(':', $request->url_drapeau);
            if(sizeof( $tab)>1){
                $name = time().'.' . explode('/', explode(':', substr($request->url_drapeau, 0, strpos($request->url_drapeau, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_drapeau=$request->get('code_icao').".". $extension;
                \Image::make($request->url_drapeau)->save(public_path('images/module/flag/'). $url_drapeau);          
                $url_drapeau=  "/images/module/flag/".$url_drapeau;
                $request->offsetSet('url_drapeau', $url_drapeau);
            }
        }

        $pays->update($request->all());

        $log= new Log();
        $log->action='Modification produit';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($pays, 'Pays Information has been updated');
    }
}
