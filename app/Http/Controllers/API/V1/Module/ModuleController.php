<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Module;
use App\Models\Module\Menu;
use App\Models\Module\Sousmenu;
use App\Models\Module\ControllerErp;
use App\Models\Module\Fonctionnalite;
use App\Models\Module\Profile;
use App\Models\Module\Log;
use App\Models\User;
use App\Models\Module\Profile_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Support\Facades\Auth;
class ModuleController extends BaseController
{
    protected $module = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Module $module)
    {
        $this->middleware('auth:api');
        $this->module = $module;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = $this->module->latest()->paginate(10);

        return $this->sendResponse($modules, 'Module list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $module = $this->module->pluck('nom', 'id');

        return $this->sendResponse($module, 'Module list');
    }


    public function list_by_user()
    {
     
        if (Auth::check()) {
            $profiles =Profile_User::
            join('users','users.id','profile_user.user_id')
          ->join('profiles',function($join){
               $join->on("profile_user.profile_id","profiles.id")
                    ->on("profile_user.user_id","=","users.id");
                   }
           )  
          ->join('fonctionnalite_profile','fonctionnalite_profile.profile_id','profile_user.profile_id')
          ->join('fonctionnalites', function($join){
           $join->on("fonctionnalite_profile.profile_id","profiles.id")
           ->on("fonctionnalite_profile.fonctionnalite_id","=","fonctionnalites.id");
          })
          ->join('controllers','controllers.id','fonctionnalites.controller_id')
          ->join('modules','modules.id','controllers.module_id')
          ->join('menus','menus.module_id','modules.id')
          ->join('sousmenus','sousmenus.menu_id','sousmenus.id')
          ->where('profile_user.user_id',Auth::user()->id)
          ->select('modules.*')
          ->get();
        }else{
            $profiles=[77];
        }
       
      // var_dump($profiles);
      // die();
    /*  function($join){
        $join->on("jobs.user_id","=","users.id")
            ->on("jobs.item_id","=","items.id");
    }*/
        
        return $this->sendResponse($profiles, 'Module by of user');
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
            //'url_fdescriptif'=>'mimes:json'

        ]);


        $url_icon="default_icon.jpg";
        if($request->url_icon){
            $name = time().'.' . explode('/', explode(':', substr($request->url_icon, 0, strpos($request->url_icon, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_icon=$request->get('nom').".". $extension;
            \Image::make($request->url_icon)->save(public_path('images/module/'). $url_icon);          
        }

        $module = $this->module->create([
            'nom' => $request->get('nom'),
            'description' => $request->get('description'),
            'icon' => $request->get('icon'),
            'url_icon' =>'images/module/'. $url_icon,
            'api_url' => $request->get('api_url'),
            'api_username' => $request->get('api_username'),
            'api_password' => $request->get('api_password'),
            'version' => $request->get('version'),
            'actif' => $request->get('actif')
        ]);



        Storage::put('public/moduleconf/'.$module->id.'.json',$request->get('file_content'));
        $log= new Log();
        $log->action="Ajout de module";
        $log->enregistrement=$module->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($module,'Module Created Successfully');
    }
    
    public function show($id){
        
        $module= Module::findOrFail($id);
        return $this->sendResponse($module, 'Module found');
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
        $module = $this->module->findOrFail($id);


        $url_icon="default_icon.jpg";
        if($request->url_icon){


            $tab= explode(':', $request->url_icon);
            if(sizeof( $tab)>1){
                $name = time().'.' . explode('/', explode(':', substr($request->url_icon, 0, strpos($request->url_icon, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_icon=$request->get('code_icao').".". $extension;
                \Image::make($request->url_icon)->save(public_path('images/module/'). $url_icon);          
                $url_icon=  "/images/module/".$url_icon;
                $request->offsetSet('url_icon', $url_icon);
            }            
        }



        $module->update($request->all());
        $log= new Log();
        $log->action="Modification de module";
        $log->enregistrement=$module->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($module, 'Module Information has been updated');
    }
    
    public function uploadsetting(Request $request)
    {

        $this->validate($request, [
            'file'=>'required|mimes:txt,json|max:4096'
        ]);
      
        $module = $this->module->findOrFail($request->get('id'));
       // Storage::put('public/moduleconf/'.$module->id.'.json',$request->get('file_content'));
       //  $fileName = auth()->id() . '_' . time() . '.'. $request->file->extension();  
       if ($request->hasFile('file')) {
        $fileName = auth()->id(). '.'. $request->file->extension();  
        $type = $request->file->getClientMimeType();
        $size = $request->file->getSize();
        $request->file->move(public_path('files/module'), $fileName);
        // Lecture du fichier json

        $path = public_path('files/module/').$fileName; 
        $json = json_decode(file_get_contents($path), true);      
       if (array_key_exists('module',$json)){
        $modules=$json['module'];
        // S'assurer que le id menus est défine dans le fichier json
        if(isset($modules['id'])){
            $this->module->id=$modules['id'];
        }
        // S'assurer que le clé nom est défine dans le fichier json
        if(isset($modules['nom'])){
            $this->module->nom=$modules['nom'];
        }
        // mise à jour ou création du module
        if(isset($modules['id']) && isset($modules['nom']) ){
            $module_db = Module::updateOrCreate(
                ['id' => $modules['id']], // This is the key of search
                ['nom'=>$modules['nom']],
             );
        }

        $menus=[];
        // S'assurer que le clé menus est défine dans le fichier json
        if(array_key_exists('menus',$modules)){
            $menus=$modules['menus'];    
        }
  
        // Pour chaque menu on recupère les sous menu
        $sousmenus=[];
        $controllerserps=[];
        foreach($menus as $key=>$_menu){ 
            $menu_db = Menu::updateOrCreate(
               ['id' => $_menu['id']], // This is the key of search
               [//'id'=>$_menu['id'],
               'nom'=>$_menu['nom'],
               'url'=>$_menu['url'],
               'actif'=> (int)$_menu['actif'],
               'module_id'=>$this->module->id],
            );

            // Si les sousmenus sont définis
            if(array_key_exists('sousmenus',$_menu)){
            $menu= (object) $_menu; 
            $sous_menus=$_menu['sousmenus'];
             // Recupération de tous les sous menus
             foreach($sous_menus as $key=>$_sousmenu){
                $unsousmenu= new Sousmenu();
                $unsousmenu->id=$_sousmenu['id'];
                $unsousmenu->nom=$_sousmenu['nom'];
                $unsousmenu->url=$_sousmenu['url'];
                $unsousmenu->actif=(int)$_sousmenu['actif'] ;
                $unsousmenu->menu_id=$menu->id;
                $sousmenu_db = Sousmenu::updateOrCreate(
                    ['id' => $_sousmenu['id']], // This is the key of search
                    ['nom'=>$_sousmenu['nom'],
                    'url'=>$_sousmenu['url'],
                    'actif'=>(int)$_sousmenu['actif'],
                    'menu_id'=>$_menu['id']],
                 );

              //  array_push( $sousmenus,$unsousmenu);


             }
            }

        }


         // Si les controllers sont définis
         if(isset($modules['controllers'])){       
            $controllererp_s=$modules['controllers'];
             // Recupération de tous les controllers
             foreach($controllererp_s as $key=>$_controller){
                 // Les objets sont crées pour un usage future
              /*  $uncontrollerErp= new ControllerErp();
                $uncontrollerErp->id=$_controller['id'];
                $uncontrollerErp->nom=$_controller['nom'];
                $uncontrollerErp->code=$_controller['code'];
                $uncontrollerErp->actif=(int)$_controller['actif'];
                $uncontrollerErp->module_id=$this->module->id;*/
                //array_push( $controllerserps,$uncontrollerErp);
                $controllererp_db = ControllerErp::updateOrCreate(
                    ['id' => $_controller['id']], // This is the key of search
                    ['nom'=>$_controller['nom'],
                    'code'=>$_controller['code'],
                    'actif'=>(int)$_controller['actif'],
                    'module_id'=>$this->module->id],
                 );

                 if(array_key_exists('fonctionnalites',$_controller)){
                    $fonctionnalites=$_controller['fonctionnalites'];
                    foreach($fonctionnalites as $key=>$_fonctionnalite){
                        $fonctionnalite_db = Fonctionnalite::updateOrCreate(
                            ['nom' => $_fonctionnalite['nom'],
                            'controller_id'=>$_controller['id']
                        ], // This is the key of search
                            ['nom'=>$_fonctionnalite['nom'],
                            'actif'=>(int)$_fonctionnalite['actif'],
                            'controller_id'=>$_controller['id']],
                         );     
        
                     }
                }





             }
        }



        $log= new Log();
        $log->action="Configuration de module";
        $log->enregistrement=$module->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
      

        // TEST DE GENERATION
       // $this->generateSettingFile( $this->module->id);
             
       }
        

       }
       return $this->sendResponse($module, 'Le parametrage a été mis à jour avec succès !');
    }


    public function downloadsetting(Request $request,$module_id){
        
        $tab=array();
        $module = Module::find($module_id);
        $tmodule=array();
        $tmodule['id']= $module->id;
        $tmodule['nom']= $module->nom;
          // Récupération des menus lié au module
         $menus = Menu::where('module_id', $module_id)->get();
         $tmodule['menus']=[];
         $men_smen_ctr_all=[]; // contient les menus et leurs sous menus
 
         foreach($menus as $key=>$_menu){
         //  Ici nous faisons une conversion vers le types objet afin de pouvoir 
         // définir facilement des attributs qui n'étaient pas  préalableble définis,
         // par exemple l'attribut sousmenus pour l'objet menu 
         $o_menu = (object)$_menu;
         //Suppression des attributs created_at,updated_at et module_id
 
         // Récupérer les sous menus d'un menu    
         $sousmenus = Sousmenu::where('menu_id', $_menu['id'])->get();
 
         // affectation des sous menus du menu
         $o_menu->sousmenus= $sousmenus;
         
 
         // Récupérer les controllers d'un menu
         // $controllers_rps = ControllerErp::where('module_id', $module_id)->get();
         // affectation des controllers du module
        // $o_menu->controllers= $controllers_rps;
         //Association menu,sous_menus et controller
         array_push($men_smen_ctr_all,$o_menu); 
         }
 
        $tmodule['menus']= $men_smen_ctr_all;      
        // Récupérer les controllers d'un menu
        $controller_erps = ControllerErp::where('module_id', $module_id)->get();
         // begin update
        foreach($controller_erps as $key=>$_controller_erp){
            $o_controller_erp = (object)$_controller_erp;

            $fonctionnalites = Fonctionnalite::where('controller_id',
             $o_controller_erp->id)->get();

            $o_controller_erp->fonctionnalites=$fonctionnalites;
        }
        // end update
        
        $tmodule['controllers']=  $controller_erps;
  
        $json = json_encode(['module'=>$tmodule],JSON_UNESCAPED_UNICODE);
        $path = public_path('files/module/generated_').$module->id.".json"; 
        File::put($path, $json);
       
        return response()->download($path);
    }
    


    public function destroy($id)
    {


        $module = $this->module->findOrFail($id);
        $module->delete();

        $log= new Log();
        $log->action="Suppression de module";
        $log->enregistrement=$module->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($module, 'Module has been Deleted');
    }


    
}
