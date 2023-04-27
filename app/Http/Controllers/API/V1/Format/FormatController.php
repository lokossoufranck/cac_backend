<?php

namespace App\Http\Controllers\API\V1\Format;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Format\Format;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class FormatController extends BaseController
{
    protected $format = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Format $format)
    {
        $this->middleware('auth:api');
        $this->format = $format;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $formats = Format::orderBy('formats.created_at','ASC')
                                  //->where('status', '=', true)
                                ->paginate(3, array('formats.*'))
                              //  ->get()
                                ;


        return $this->sendResponse($formats, 'Format list');
    }

    public function list(){
        $formats = Format::orderBy('formats.created_at','ASC')
        //->where('status', '=', true)
          ->get()
      ;


return $this->sendResponse($formats, 'Format list');

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
        $format= new Format();
        $format->name=$request->get('name');
        $format->code = $request->get("code");
        $format->status = $request->get("status");
        $format->save();
        return $this->sendResponse($format, 'Format Created Successfully');
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
    
        $format = $this->format->findOrFail($id);
        $format->update($request->all());


        $log= new Log();
        $log->action='Modification Format';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($format, 'Format Informat has been updated');
    }


    public function destroy($id)
    {
        $format = $this->format->findOrFail($id);
        if( $format->status){
            $format->status=false;
        }else{
            $format->status=true;
        }
  
        $format->save();
        $log= new Log();
        $log->action="Suppression de Format";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($format, 'Format has been Deleted');
    }
}
