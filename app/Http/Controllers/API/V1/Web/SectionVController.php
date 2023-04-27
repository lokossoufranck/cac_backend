<?php

namespace App\Http\Controllers\API\V1\Web;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Web\SectionV;
use App\Models\Web\Page;
use App\Models\Module\Log;
use App\Models\Format\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SectionVController extends BaseController
{
    protected $sectionv = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SectionV $sectionv)
    {
       // $this->middleware('auth:api');
        $this->sectionv = $sectionv;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $section_v_s = SectionV::select("section_v_s.*",'formats.name as format_name')
         ->join('formats', 'formats.id', '=', 'section_v_s.format_id')
         ->orderBy('section_v_s.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(10, array('section_v_s.*'))
                                ;


        return $this->sendResponse($section_v_s, 'SectionV list');
    }

    public function list()
    {

        $section_v_s = $this->sectionv->get(['title_1', 'id']);
        return $this->sendResponse($section_v_s, 'SectionV list');
       
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
            'code' => 'required|unique:section_v_s',
            'title_1' => 'required',
           // 'data_sectionv'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_sectionv'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $url="default";
      
        $sectionv= new SectionV();
        $upload_path = public_path('section_v_s');
        if($request->hasFile('data_sectionv')){
            $file_name = $request->data_sectionv->getClientOriginalName();
            $ext=$request->data_sectionv->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $sectionv->url =  $generated_new_name;
            $request->data_sectionv->move($upload_path, $generated_new_name);
           // $request->data_sectionv->storeAs('public', 'file.txt');
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
            $sectionv->adresse = $request->get("adresse");
            $sectionv->url =  $url;
        }


   
        $sectionv->title_1=$request->get('title_1');
        $sectionv->title_2=$request->get('title_2');
        $sectionv->code = $request->get("code");
        $sectionv->format_id = $request->get("format_id");
        $sectionv->data_sectionv = $request->get("data_sectionv");
        $sectionv->format_id = $request->get("format_id");   
        $sectionv->description_1 = $request->get("description_1"); 
        $sectionv->description_2 = $request->get("description_2"); 
        $sectionv->page_id=$request->get('page_id');       
        $sectionv->save();
      
      
     

        return $this->sendResponse($sectionv, 'SectionV Created Successfully');
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
    
        $sectionv = $this->sectionv->findOrFail($id);
        $this->validate($request, [
            'title_1' => 'required',
           // 'data_sectionv'=> 'file|required',
        ]);

      /*  if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_sectionv'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }*/

        
        
        $upload_path = public_path('section_v_s');
        if($request->hasFile('data_sectionv')){
            $file_name = $request->data_sectionv->getClientOriginalName();
            $ext=$request->data_sectionv->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $sectionv->url =  $generated_new_name;
            $request->data_sectionv->move($upload_path, $generated_new_name);
           // $request->data_sectionv->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                   $sectionv->format_id = $format->id ;
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
                $sectionv->format_id = $format->id ;
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
            $sectionv->format_id = $format->id ;
           }
        }else{ // il s'agit d'un lien
           // $format=Format::where('name','lien')->first();
          //  $sectionv->adresse = $request->get("adresse");
           // $sectionv->url =  $url;

        }


      
        $sectionv->title_1=$request->get('title_1');
        $sectionv->title_2=$request->get('title_2');
        $sectionv->code = $request->get("code");
        $sectionv->data_sectionv = $request->get("data_sectionv");
        $sectionv->description_1 = $request->get("description_1");   
        $sectionv->description_2 = $request->get("description_2"); 
        $sectionv->page_id=$request->get('page_id');   
        $sectionv->save();
        
       // $sectionv->update($request->all());

        $log= new Log();
        $log->action='Modification SectionV';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($sectionv, 'SectionV Insectionv has been updated');
    }


    public function destroy($id)
    {
        $sectionv = $this->sectionv->findOrFail($id);
        $sectionv->delete();
        $log= new Log();
        $log->action="Suppression de SectionV";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($sectionv, 'SectionV has been Deleted');
    }
	public function show($id)
    {
        $sectionv = $this->sectionv->findOrFail($id);

        return $this->sendResponse($sectionv, 'sectionv Details');
    }
    public function download_sectionv(Request $request,$url){
        $path = public_path('section_v_s/').$url;  
        return response()->download($path);
    }
}
