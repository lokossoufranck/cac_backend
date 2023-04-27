<?php

namespace App\Http\Controllers\API\V1\Formation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Formation\Modulef;
use App\Models\Formation\Formation_Modulef;
use App\Models\Formation\Modulef_Whatslearn;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ModulefController extends BaseController
{
    protected $modulef = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Modulef $modulef)
    {
        $this->middleware('auth:api');
        $this->modulef = $modulef;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  

   
      //  $modulefs;
         $modulefs = $this->modulef->latest()->with('formations:id,name as text,code', 'whatslearns:id,name as text')->paginate(10);
         /*$modulefs = Modulef::orderBy('modulefs.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(3, array('modulefs.*'))
                            //    ->get()
                                ;*/
        return $this->sendResponse($modulefs, 'Modulef list');
    }

    
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {

        $modulefs = $this->modulef->get(['title', 'id']);
        return $this->sendResponse($modulefs, 'Modulef list');
       
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
        
        
        try{

  
        DB::beginTransaction();
        $modulef= new Modulef();
        $modulef->title=$request->get('title');
        $modulef->code = $request->get("code");
        $modulef->prix = $request->get("prix");
        $modulef->duration = $request->get("duration");
        $modulef->order = $request->get("order");
        $modulef->status = $request->get("status");
        $modulef->description_photo_1 = $request->get("description_photo_1");
        $modulef->description_photo_2 = $request->get("description_photo_2");      
        $modulef->lang = $request->get("lang");
        $modulef->is_deleted=false;

        $url_photo_1="default.png";
        $url_photo_2="default.png";

        if($request->url_photo_1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_photo_1, 0, strpos($request->url_photo_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_1="1_".$request->get('code').".". $extension;
            \Image::make($request->url_photo_1)->save(public_path('modulefs/'). $url_photo_1);
            $modulef->url_photo_1 =$url_photo_1;          
        }

        if($request->url_photo_2){
            $name = time().'.' . explode('/', explode(':', substr($request->url_photo_2, 0, strpos($request->url_photo_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_2="1_".$request->get('code').".". $extension;
            \Image::make($request->url_photo_2)->save(public_path('modulefs/'). $url_photo_2);
            $modulef->url_photo_2 =$url_photo_2;          
        }

        $modulef->save();

         // update pivot table
         $formations_ids = [];
         foreach ($request->get('formations') as $formation) {
            $formations_ids[]= $formation['id'];

         }
         $modulef->formations()->sync($formations_ids);

         $whatslearns_ids = [];
         foreach ($request->get('whatslearns') as $whatslearn) {
            $whatslearns_ids[]= $whatslearn['id'];
         }
         $modulef->whatslearns()->sync($whatslearns_ids);
         DB::commit();

         $log= new Log();
         $log->action='Enregistrement Modulef';
         $log->enregistrement=$id;
         $log->user_id=\Auth::user()->id;
         $log->save();

        return $this->sendResponse($modulef, 'Modulef Created Successfully');    

        }catch(\Exception $e){
            return $this->sendResponse($e, 'Modulef Errors'); 
            DB::rollback();
        }
       
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
        try{
            DB::beginTransaction();
            $modulef = $this->modulef->findOrFail($id);
            $_data=$request->all();

            if($request->url_photo_1){
                $name = time().'.' . explode('/', explode(':', substr($request->url_photo_1, 0, strpos($request->url_photo_1, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_photo_1="1_".$request->get('code').".". $extension;
                \Image::make($request->url_photo_1)->save(public_path('modulefs/'). $url_photo_1);
                $modulef->url_photo_1 =$url_photo_1; 
                $_data['url_photo_1']  =$url_photo_1;       
            }

            if($request->url_photo_2){
                $name = time().'.' . explode('/', explode(':', substr($request->url_photo_2, 0, strpos($request->url_photo_2, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_photo_2="1_".$request->get('code').".". $extension;
                \Image::make($request->url_photo_2)->save(public_path('modulefs/'). $url_photo_2);
                $modulef->url_photo_2 =$url_photo_2; 
                $_data['url_photo_2']  =$url_photo_2;       
            }


             $modulef->update($_data);
                    // update pivot table
                $formations_ids = [];
                foreach ($request->get('formations') as $formation) {
                    $formations_ids[]= $formation['id'];

                }
                $modulef->formations()->sync($formations_ids);

                $whatslearns_ids = [];
                foreach ($request->get('whatslearns') as $whatslearn) {
                    $whatslearns_ids[]= $whatslearn['id'];
                }
                $modulef->whatslearns()->sync($whatslearns_ids);
            
                    $log= new Log();
                    $log->action='Modification Modulef';
                    $log->enregistrement=$id;
                    $log->user_id=\Auth::user()->id;
                    $log->save();
            
                    // update pivot table
                    
                    DB::commit();
                    return $this->sendResponse($modulef, 'Modulef Created Successfully');    
            
                    }catch(\Exception $e){
                        return $this->sendResponse($e->getMessage(), 'Modulef Errors'); 
                        DB::rollback();
                    }
    
       

        return $this->sendResponse($modulef, 'Modulef Inmodulef has been updated');
    }


    public function destroy($id)
    {
        $modulef = $this->modulef->findOrFail($id);
        if( $modulef->status){
            $modulef->status=false;
        }else{
            $modulef->status=true;
        }
  
        $modulef->save();
        $log= new Log();
        $log->action="Suppression de Modulef";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($modulef, 'Modulef has been Deleted');
    }
}