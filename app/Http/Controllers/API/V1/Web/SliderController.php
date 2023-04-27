<?php

namespace App\Http\Controllers\API\V1\Web;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Web\Slider;
use App\Models\Module\Log;
use App\Models\Format\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SliderController extends BaseController
{
    protected $slider = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Slider $slider)
    {
        //$this->middleware('auth:api');
        $this->slider = $slider;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $sliders = Slider::select("sliders.*",'formats.name as format_name')
         ->join('formats', 'formats.id', '=', 'sliders.format_id')
         ->orderBy('sliders.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(10, array('sliders.*'))
                                ;


        return $this->sendResponse($sliders, 'Slider list');
    }

    public function list()
    {

        $sliders = $this->slider->get(['title_1', 'id']);
        return $this->sendResponse($sliders, 'Slider list');
       
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
            'code' => 'required|unique:sliders',
            'title_1' => 'required',
           // 'data_slider'=> 'file|required',
        ]);

        if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_slider'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }

        
        
        $url="default";
      
        $slider= new Slider();
        $upload_path = public_path('sliders');
        if($request->hasFile('data_slider')){
            $file_name = $request->data_slider->getClientOriginalName();
            $ext=$request->data_slider->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $slider->url =  $generated_new_name;
            $request->data_slider->move($upload_path, $generated_new_name);
           // $request->data_slider->storeAs('public', 'file.txt');
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
            $slider->adresse = $request->get("adresse");
            $slider->url =  $url;
        }


   
        $slider->title_1=$request->get('title_1');
        $slider->title_2=$request->get('title_2');
        $slider->code = $request->get("code");
        $slider->format_id = $request->get("format_id");
        $slider->data_slider = $request->get("data_slider");
        $slider->format_id = $request->get("format_id");   
        $slider->description = $request->get("description");       
        $slider->save();
       // $base64data = base64_decode(, true);
       // file_put_contents('file.pdf', $request->get("url"));
      
     

        return $this->sendResponse($slider, 'Slider Created Successfully');
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
    
        $slider = $this->slider->findOrFail($id);
        $this->validate($request, [
            'title_1' => 'required',
           // 'data_slider'=> 'file|required',
        ]);

      /*  if ( $request->format_id <=2 ){
            $this->validate($request, [
                'data_slider'=> 'file|required',
            ]);
        }else{
            $this->validate($request, [
                'adresse'=> 'required',
            ]);
        }*/

        
        
        $upload_path = public_path('sliders');
        if($request->hasFile('data_slider')){
            $file_name = $request->data_slider->getClientOriginalName();
            $ext=$request->data_slider->getClientOriginalExtension();
            
            $generated_new_name = $request->get("code") . '.' . $ext ;
            $slider->url =  $generated_new_name;
            $request->data_slider->move($upload_path, $generated_new_name);
           // $request->data_slider->storeAs('public', 'file.txt');
           try{
            $filetype = getimagesize( $upload_path."/".$generated_new_name);
            if(is_array($filetype)){
                if(in_array($filetype[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
                {
                // The format is image
                   $format=Format::where('name','image')->first();
                   $slider->format_id = $format->id ;
                }
            }else{
                  // The format is pdf
                $format=Format::where('name','pdf')->first();
                $slider->format_id = $format->id ;
            }
            

           }catch(Exception $e){
            $format=Format::where('name','pdf')->first();
            $slider->format_id = $format->id ;
           }
        }else{ // il s'agit d'un lien
           // $format=Format::where('name','lien')->first();
          //  $slider->adresse = $request->get("adresse");
           // $slider->url =  $url;

        }


      
        $slider->title_1=$request->get('title_1');
        $slider->title_2=$request->get('title_2');
        $slider->code = $request->get("code");
        $slider->data_slider = $request->get("data_slider");
        $slider->description = $request->get("description");       
        $slider->save();
        
       // $slider->update($request->all());

        $log= new Log();
        $log->action='Modification Slider';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($slider, 'Slider Inslider has been updated');
    }


    public function destroy($id)
    {
        $slider = $this->slider->findOrFail($id);
        $slider->delete();
        $log= new Log();
        $log->action="Suppression de Slider";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($slider, 'Slider has been Deleted');
    }
    public function download_slider(Request $request,$url){
        $path = public_path('sliders/').$url;  
        return response()->download($path);
    }
}
