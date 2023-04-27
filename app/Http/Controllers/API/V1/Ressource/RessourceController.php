<?php

namespace App\Http\Controllers\API\V1\Ressource;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Ressource\Ressource;
use App\Models\Module\Log;
use App\Models\Format\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class RessourceController extends BaseController
{
    protected $ressource = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Ressource $ressource)
    {
        $this->middleware('auth:api');
        $this->ressource = $ressource;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $ressources = Ressource::select("ressources.*",'formats.name as format_name')
         ->join('formats', 'formats.id', '=', 'ressources.format_id')
         ->orderBy('ressources.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(10, array('ressources.*'))
                            //    ->get()
                                ;


        return $this->sendResponse($ressources, 'Ressource list');
    }

    public function list()
    {

        $ressources = $this->ressource->get(['name', 'id']);
        return $this->sendResponse($ressources, 'Ressource list');
       
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
            'code' => 'required|unique:ressources',
            'name' => 'required',
           // 'data_ressource'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_ressource'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $url="default";
      
        $ressource= new Ressource();
        $upload_path = public_path('ressources');
        if($request->hasFile('data_ressource')){
            $file_name = $request->data_ressource->getClientOriginalName();
            $ext=$request->data_ressource->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $ressource->url =  $generated_new_name;
            $request->data_ressource->move($upload_path, $generated_new_name);
           // $request->data_ressource->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
           }
        }else{ // il s'agit d'un lien
            $format=Format::where('name','lien')->first();
            $ressource->adresse = $request->get("adresse");
            $ressource->url =  $url;
        }


   
        $ressource->name=$request->get('name');
        $ressource->code = $request->get("code");
        $ressource->format_id = $request->get("format_id");
        $ressource->data_ressource = $request->get("data_ressource");
        $ressource->format_id = $request->get("format_id");   
        $ressource->description = $request->get("description");       
        $ressource->save();
       // $base64data = base64_decode(, true);
       // file_put_contents('file.pdf', $request->get("url"));
      
     

        return $this->sendResponse($ressource, 'Ressource Created Successfully');
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
    
        $ressource = $this->ressource->findOrFail($id);
        $this->validate($request, [
            'name' => 'required',
            'data_ressource'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_ressource'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $upload_path = public_path('ressources');
        if($request->hasFile('data_ressource')){
            $file_name = $request->data_ressource->getClientOriginalName();
            $ext=$request->data_ressource->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $ressource->url =  $generated_new_name;
            $request->data_ressource->move($upload_path, $generated_new_name);
           // $request->data_ressource->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
           }
        }else{ // il s'agit d'un lien
            $format=Format::where('name','lien')->first();
            $ressource->adresse = $request->get("adresse");
            $ressource->url =  $url;
        }


        $ressource->format_id = $format->id ;
        $ressource->name=$request->get('name');
        $ressource->code = $request->get("code");
        $ressource->data_ressource = $request->get("data_ressource");
       // $ressource->format_id = $request->get("format_id");   
        $ressource->description = $request->get("description");       
        $ressource->save();
        
       // $ressource->update($request->all());


        $log= new Log();
        $log->action='Modification Ressource';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($ressource, 'Ressource Inressource has been updated');
    }


    public function destroy($id)
    {
        $ressource = $this->ressource->findOrFail($id);
        $ressource->delete();
        $log= new Log();
        $log->action="Suppression de Ressource";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($ressource, 'Ressource has been Deleted');
    }
    public function download_ressource(Request $request,$url){
        $path = public_path('ressources/').$url;  
        return response()->download($path);
    }
}
