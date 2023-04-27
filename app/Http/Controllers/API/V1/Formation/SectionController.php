<?php

namespace App\Http\Controllers\API\V1\Formation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Formation\Section;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SectionController extends BaseController
{
    protected $section = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Section $section)
    {
        $this->middleware('auth:api');
        $this->section = $section;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $sections = Section::orderBy('sections.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(3, array('sections.*'))
                              //  ->get()
                                ;


        return $this->sendResponse($sections, 'Section list');
    }

    public function list(){
        $sections = Section::orderBy('sections.created_at','ASC')
          ->get()
      ;


return $this->sendResponse($sections, 'Section list');

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
        $section= new Section();
        $section->name=$request->get('name');
        $section->code = $request->get("code");
        $section->status = $request->get("status");
        $section->save();
        return $this->sendResponse($section, 'Section Created Successfully');
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
    
        $section = $this->section->findOrFail($id);
        $section->update($request->all());


        $log= new Log();
        $log->action='Modification Section';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($section, 'Section Insection has been updated');
    }


    public function destroy($id)
    {
        $section = $this->section->findOrFail($id);
        if( $section->status){
            $section->status=false;
        }else{
            $section->status=true;
        }
  
        $section->save();
        $log= new Log();
        $log->action="Suppression de Section";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($section, 'Section has been Deleted');
    }
}
