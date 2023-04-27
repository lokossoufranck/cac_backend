<?php

namespace App\Http\Controllers\API\V1\Module;
use App\Http\Requests\Module\SiteRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Site;
use App\Models\Module\Pays;
use App\Models\Module\Log;
use Illuminate\Http\Request;

class SiteController extends BaseController
{
    protected $site = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Site $site)
    {
        // $this->middleware('auth:api');
        $this->site = $site;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$sites = $this->site->orderBy('nom', 'ASC')->latest()->paginate(100);
        $sites = Site::join('pays', 'pays.id', '=', 'sites.pays_id')
        ->paginate(10, array('sites.*', 'pays.nom as pay_nom'));
        return $this->sendResponse($sites, 'Site list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $sites = $this->site->pluck('nom', 'id');
        return $this->sendResponse($sites, 'Site list');
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
            'nom' => 'required|unique:sites',
            'tel1' => 'required',
            'actif' => 'required',
        ]);

        $url_logo="default_logo.jpg";
        if($request->url_logo){
            $name = time().'.' . explode('/', explode(':', substr($request->url_logo, 0, strpos($request->url_logo, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_logo=$request->get('nom').".". $extension;
            \Image::make($request->url_logo)->save(public_path('images/module/logo/'). $url_logo);      
        }


 
        $url_header_1="default_header.jpg";
        if($request->url_header_1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_header_1, 0, strpos($request->url_header_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_header_1=$request->get('nom').".". $extension;
            \Image::make($request->url_header_1)->save(public_path('images/module/header/'). $url_header_1);      
        }

		$url_header_2="default_header.jpg";
        if($request->url_header_2){
            $name = time().'.' . explode('/', explode(':', substr($request->url_header_2, 0, strpos($request->url_header_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_header_2=$request->get('nom').".". $extension;
            \Image::make($request->url_header_2)->save(public_path('images/module/header/'). $url_header_2);      
        }

        $url_footer_1="default_footer.jpg";
        if($request->url_footer_1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_footer_1, 0, strpos($request->url_footer_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_footer_1=$request->get('nom').".". $extension;
            \Image::make($request->url_footer_1)->save(public_path('images/module/footer/'). $url_footer_1);      
        }

        $url_footer_2="default_footer.jpg";
        if($request->url_footer_2){
            $name = time().'.' . explode('/', explode(':', substr($request->url_footer_2, 0, strpos($request->url_footer_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_footer_2=$request->get('nom').".". $extension;
            \Image::make($request->url_footer_2)->save(public_path('images/module/footer/'). $url_footer_2);      
        }


        $site= new Site();
        $site->nom=$request->get('nom');
        $site->sigle=$request->get('sigle');
        $site->adresse=$request->get('adresse');


        $site->tel1=$request->get('tel1');
        $site->tel2=$request->get('tel2');
        $site->email=$request->get('email');
        $site->site_web=$request->get('site_web');
        $site->pays_id=$request->get('pays_id');
        $site->is_siege=$request->get('is_siege');
        $site->url_logo=  "/images/module/logo/".$url_logo;
        $site->url_header_1=  "/images/module/header/".$url_header_1;
        $site->url_header_2=  "/images/module/header/".$url_header_2;
        $site->url_footer_1=  "/images/module/footer/".$url_footer_1;
        $site->url_footer_2=  "/images/module/footer/".$url_footer_2;
        $site->actif=$request->get('actif');
        $site->save();
        return $this->sendResponse($site, 'Site Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(SiteRequest $request, $id)
    
    {

        $site = $this->site->findOrFail($id);

        $url_logo="default_logo.jpg";
        if($request->url_logo){
            $tab= explode(':', $request->url_logo);
            if(sizeof( $tab)>1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_logo, 0, strpos($request->url_logo, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_logo=$request->get('nom').".". $extension;
            \Image::make($request->url_logo)->save(public_path('images/module/logo/'). $url_logo);     
            $url_logo=  "/images/module/logo/".$url_logo;
            $request->offsetSet('url_logo', $url_logo); 
            }
        }


 
        $url_header_1="default_header.jpg";
        if($request->url_header_1){
            $tab= explode(':', $request->url_header_1);
            if(sizeof( $tab)>1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_header_1, 0, strpos($request->url_header_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_header_1=$request->get('nom').".". $extension;
            \Image::make($request->url_header_1)->save(public_path('images/module/header/'). $url_header_1); 
            $url_header_1=  "/images/module/header/".$url_header_1;
            $request->offsetSet('url_header_1', $url_header_1); 
            }    
        }

		$url_header_2="default_header.jpg";
        if($request->url_header_2){
            $tab= explode(':', $request->url_header_2);
            if(sizeof( $tab)>1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_header_2, 0, strpos($request->url_header_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_header_2=$request->get('nom').".". $extension;
            \Image::make($request->url_header_2)->save(public_path('images/module/header/'). $url_header_2); 
            $url_header_2=  "/images/module/header/".$url_header_2;
            $request->offsetSet('url_header_2', $url_header_2);   
            }       
        }

        $url_footer_1="default_footer.jpg";
        if($request->url_footer_1){
            $tab= explode(':', $request->url_footer_1);
            if(sizeof( $tab)>1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_footer_1, 0, strpos($request->url_footer_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_footer_1=$request->get('nom').".". $extension;
            \Image::make($request->url_footer_1)->save(public_path('images/module/footer/'). $url_footer_1);  
            $url_footer_1=  "/images/module/footer/".$url_footer_1;
            $request->offsetSet('url_footer_1', $url_footer_1); 
            }      
        }

        $url_footer_2="default_footer.jpg";
        if($request->url_footer_2){
            $tab= explode(':', $request->url_footer_2);
            if(sizeof( $tab)>1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_footer_2, 0, strpos($request->url_footer_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_footer_2=$request->get('nom').".". $extension;
            \Image::make($request->url_footer_2)->save(public_path('images/module/footer/'). $url_footer_2);   
            $url_footer_2=  "/images/module/footer/".$url_footer_2;
            $request->offsetSet('url_footer_2', $url_footer_2);     
            }  
        }

        $site->update($request->all());
        $log= new Log();
        $log->action='Modification site';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($site, 'Site Information has been updated');
    }


    public function destroy($id)
    {
        $site = $this->site->findOrFail($id);
        $site->delete();

        $log= new Log();
        $log->action="Suppression de site";
        $log->enregistrement=$site->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($site, 'Site has been Deleted');
    }
}
