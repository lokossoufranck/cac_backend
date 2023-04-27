<?php

namespace App\Http\Controllers\API\V1\Formation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Formation\Lesson;
use App\Models\Formation\Lesson_Ressource;
use App\Models\Formation\Lesson_Modulef;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LessonController extends BaseController
{
    protected $lesson = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Lesson $lesson)
    {
        $this->middleware('auth:api');
        $this->lesson = $lesson;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  

        $lessons = $this->lesson->latest()->with('modulefs:id,title as text,code', 'ressources:id,name as text')->paginate(10);
        return $this->sendResponse($lessons, 'Lesson list');
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
        $lesson= new Lesson();
        $lesson->section_id=$request->get('section_id');
        $lesson->title=$request->get('title');
        $lesson->code = $request->get("code");
        $lesson->content = $request->get("content");
        $lesson->duration = $request->get("duration");
        $lesson->order = $request->get("order");
        $lesson->status = $request->get("status");
        $lesson->description_photo_1 = $request->get("description_photo_1");
        $lesson->description_photo_2 = $request->get("description_photo_2");      
        $lesson->lang = $request->get("lang");
        $lesson->is_deleted=false;

        $url_photo_1="default.png";
        $url_photo_2="default.png";

        if($request->url_photo_1){
            $name = time().'.' . explode('/', explode(':', substr($request->url_photo_1, 0, strpos($request->url_photo_1, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_1="1_".$request->get('code').".". $extension;
            \Image::make($request->url_photo_1)->save(public_path('lessons/'). $url_photo_1);
            $lesson->url_photo_1 =$url_photo_1;          
        }

        if($request->url_photo_2){
            $name = time().'.' . explode('/', explode(':', substr($request->url_photo_2, 0, strpos($request->url_photo_2, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_2="2_".$request->get('code').".". $extension;
            \Image::make($request->url_photo_2)->save(public_path('lessons/'). $url_photo_2);
            $lesson->url_photo_2 =$url_photo_2;          
        }

        $lesson->save();

         // update pivot table
         $modulefs_ids = [];
         foreach ($request->get('modulefs') as $modulef) {
            $modulefs_ids[]= $modulef['id'];

         }
         $lesson->modulefs()->sync($modulefs_ids);

         $ressources_ids = [];
         foreach ($request->get('ressources') as $ressource) {
            $ressources_ids[]= $ressource['id'];
         }
         $lesson->ressources()->sync($ressources_ids);
         DB::commit();

         $log= new Log();
         $log->action='Enregistrement Lesson';
         $log->enregistrement=$id;
         $log->user_id=\Auth::user()->id;
         $log->save();

        return $this->sendResponse($lesson, 'Lesson Created Successfully');    

        }catch(\Exception $e){

            return $this->sendResponse($e, $e); 
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
            $lesson = $this->lesson->findOrFail($id);
            $_data=$request->all();

            if($request->url_photo_1){
                $name = time().'.' . explode('/', explode(':', substr($request->url_photo_1, 0, strpos($request->url_photo_1, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_photo_1="1_".$request->get('code').".". $extension;
                \Image::make($request->url_photo_1)->save(public_path('lessons/'). $url_photo_1);
                $lesson->url_photo_1 =$url_photo_1; 
                $_data['url_photo_1']  =$url_photo_1;       
            }

            if($request->url_photo_2){
                $name = time().'.' . explode('/', explode(':', substr($request->url_photo_2, 0, strpos($request->url_photo_2, ';')))[1])[1];
                $extension=explode('.',  $name)[1];
                $url_photo_2="1_".$request->get('code').".". $extension;
                \Image::make($request->url_photo_2)->save(public_path('lessons/'). $url_photo_2);
                $lesson->url_photo_2 =$url_photo_2; 
                $_data['url_photo_2']  =$url_photo_2;       
            }


             $lesson->update($_data);
                    // update pivot table
                $modulefs_ids = [];
                foreach ($request->get('modulefs') as $modulef) {
                    $modulefs_ids[]= $modulef['id'];

                }
                $lesson->modulefs()->sync($modulefs_ids);

                $ressources_ids = [];
                foreach ($request->get('ressources') as $ressource) {
                    $ressources_ids[]= $ressource['id'];
                }
                $lesson->ressources()->sync($ressources_ids);
            
                    $log= new Log();
                    $log->action='Modification Lesson';
                    $log->enregistrement=$id;
                    $log->user_id=\Auth::user()->id;
                    $log->save();
            
                    // update pivot table
                    
                    DB::commit();
                    return $this->sendResponse($lesson, 'Lesson Created Successfully');    
            
                    }catch(\Exception $e){
                        return $this->sendResponse($e->getMessage(), 'Lesson Errors'); 
                        DB::rollback();
                    }
    
       

        return $this->sendResponse($lesson, 'Lesson Inlesson has been updated');
    }


    public function destroy($id)
    {
        $lesson = $this->lesson->findOrFail($id);
        $lesson->delete();

        Lesson_Modulef::where('lesson_id', $id)->delete();
        Lesson_Ressource::where('lesson_id', $id)->delete();
        $log= new Log();
        $log->action="Suppression de Lesson";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($lesson, 'Lesson has been Deleted');
    }
}