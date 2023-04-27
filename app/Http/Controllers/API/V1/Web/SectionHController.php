<?php

namespace App\Http\Controllers\API\V1\Web;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Web\SectionH;
use App\Models\Web\Page;
use App\Models\Module\Log;
use App\Models\Format\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SectionHController extends BaseController
{
    protected $sectionh = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SectionH $sectionh)
    {
       // $this->middleware('auth:api');
        $this->sectionh = $sectionh;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $section_h_s = SectionH::select("section_h_s.*",'formats.name as format_name')
         ->join('formats', 'formats.id', '=', 'section_h_s.format_id')
         ->orderBy('section_h_s.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(10, array('section_h_s.*'))
                                ;


        return $this->sendResponse($section_h_s, 'SectionH list');
    }

    public function list()
    {

        $section_h_s = $this->sectionh->get(['title_1', 'id']);
        return $this->sendResponse($section_h_s, 'SectionH list');
       
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
            'code' => 'required|unique:section_h_s',
            'title_1' => 'required',
           // 'data_sectionh'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_sectionh'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $url="default";
      
        $sectionh= new SectionH();
        $upload_path = public_path('section_h_s');
        if($request->hasFile('data_sectionh')){
            $file_name = $request->data_sectionh->getClientOriginalName();
            $ext=$request->data_sectionh->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $sectionh->url =  $generated_new_name;
            $request->data_sectionh->move($upload_path, $generated_new_name);
           // $request->data_sectionh->storeAs('public', 'file.txt');
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
            $sectionh->adresse = $request->get("adresse");
            $sectionh->url =  $url;
        }


   
        $sectionh->title_1=$request->get('title_1');
        $sectionh->title_2=$request->get('title_2');
        $sectionh->code = $request->get("code");
        $sectionh->format_id = $request->get("format_id");
        $sectionh->data_sectionh = $request->get("data_sectionh");
        $sectionh->format_id = $request->get("format_id");   
        $sectionh->description_1 = $request->get("description_1"); 
        $sectionh->description_2 = $request->get("description_2"); 
        $sectionh->page_id=$request->get('page_id');       
        $sectionh->save();
      
      
     

        return $this->sendResponse($sectionh, 'SectionH Created Successfully');
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
    
        $sectionh = $this->sectionh->findOrFail($id);
        $this->validate($request, [
            'title_1' => 'required',
           // 'data_sectionh'=> 'file|required',
        ]);

      /*  if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_sectionh'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }*/

        
        
        $upload_path = public_path('section_h_s');
        if($request->hasFile('data_sectionh')){
            $file_name = $request->data_sectionh->getClientOriginalName();
            $ext=$request->data_sectionh->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $sectionh->url =  $generated_new_name;
            $request->data_sectionh->move($upload_path, $generated_new_name);
           // $request->data_sectionh->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                   $sectionh->format_id = $format->id ;
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
                $sectionh->format_id = $format->id ;
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
            $sectionh->format_id = $format->id ;
           }
        }else{ // il s'agit d'un lien
           // $format=Format::where('name','lien')->first();
          //  $sectionh->adresse = $request->get("adresse");
           // $sectionh->url =  $url;

        }


      
        $sectionh->title_1=$request->get('title_1');
        $sectionh->title_2=$request->get('title_2');
        $sectionh->code = $request->get("code");
        $sectionh->data_sectionh = $request->get("data_sectionh");
        $sectionh->description_1 = $request->get("description_1");   
        $sectionh->description_2 = $request->get("description_2"); 
        $sectionh->page_id=$request->get('page_id');   
        $sectionh->save();
        
       // $sectionh->update($request->all());

        $log= new Log();
        $log->action='Modification SectionH';
        $log->enregistrement=$id;
        //$log->user_id=\Auth::user()->id;
        $log->user_id=1;
        $log->save();

        return $this->sendResponse($sectionh, 'SectionH Insectionh has been updated');
    }


    public function destroy($id)
    {
        $sectionh = $this->sectionh->findOrFail($id);
        $sectionh->delete();
        $log= new Log();
        $log->action="Suppression de SectionH";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($sectionh, 'SectionH has been Deleted');
    }
	public function show($id)
    {
        $sectionh = $this->sectionh->findOrFail($id);

        return $this->sendResponse($sectionh, 'sectionh Details');
    }
    public function download_sectionh(Request $request,$url){
        $path = public_path('section_h_s/').$url;  
        return response()->download($path);
    }
}
