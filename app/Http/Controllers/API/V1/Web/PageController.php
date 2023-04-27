<?php

namespace App\Http\Controllers\API\V1\Web;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Web\Page;
use App\Models\Module\Log;
use App\Models\Format\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PageController extends BaseController
{
    protected $page = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Page $page)
    {
       // $this->middleware('auth:api');
        $this->page = $page;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $pages = Page::select("pages.*",'formats.name as format_name')
         ->join('formats', 'formats.id', '=', 'pages.format_id')
         ->orderBy('pages.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(10, array('pages.*'))
                                ;


        return $this->sendResponse($pages, 'Page list');
    }

    public function list()
    {

        $pages = $this->page->get(['title_1', 'id']);
        return $this->sendResponse($pages, 'Page list');
       
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
            'code' => 'required|unique:pages',
            'title_1' => 'required',
           // 'data_page'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_page'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $url="default";
      
        $page= new Page();
        $upload_path = public_path('pages');
        if($request->hasFile('data_page')){
            $file_name = $request->data_page->getClientOriginalName();
            $ext=$request->data_page->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $page->url =  $generated_new_name;
            $request->data_page->move($upload_path, $generated_new_name);
           // $request->data_page->storeAs('public', 'file.txt');
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
            $page->adresse = $request->get("adresse");
            $page->url =  $url;
        }


   
        $page->title_1=$request->get('title_1');
        $page->title_2=$request->get('title_2');
        $page->code = $request->get("code");
        $page->format_id = $request->get("format_id");
        $page->data_page = $request->get("data_page");
        $page->format_id = $request->get("format_id");   
        $page->description = $request->get("description");       
        $page->save();

      
     

        return $this->sendResponse($page, 'Page Created Successfully');
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
    
        $page = $this->page->findOrFail($id);
        $this->validate($request, [
            'title_1' => 'required',
           // 'data_page'=> 'file|required',
        ]);

      /*  if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_page'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }*/

        
        
        $upload_path = public_path('pages');
        if($request->hasFile('data_page')){
            $file_name = $request->data_page->getClientOriginalName();
            $ext=$request->data_page->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $page->url =  $generated_new_name;
            $request->data_page->move($upload_path, $generated_new_name);
           // $request->data_page->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                   $page->format_id = $format->id ;
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
                $page->format_id = $format->id ;
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
            $page->format_id = $format->id ;
           }
        }else{ // il s'agit d'un lien
           // $format=Format::where('name','lien')->first();
          //  $page->adresse = $request->get("adresse");
           // $page->url =  $url;

        }


      
        $page->title_1=$request->get('title_1');
        $page->title_2=$request->get('title_2');
        $page->code = $request->get("code");
        $page->data_page = $request->get("data_page");
        $page->description = $request->get("description");       
        $page->save();
        
       // $page->update($request->all());

        $log= new Log();
        $log->action='Modification Page';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($page, 'Page Inpage has been updated');
    }


    public function destroy($id)
    {
        $page = $this->page->findOrFail($id);
        $page->delete();
        $log= new Log();
        $log->action="Suppression de Page";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($page, 'Page has been Deleted');
    }
	
	 public function show($id)
    {
        $page = $this->page->findOrFail($id);

        return $this->sendResponse($page, 'page Details');
    }
    public function download_page(Request $request,$url){
        $path = public_path('pages/').$url;  
        return response()->download($path);
    }
}
