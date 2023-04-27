<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\PersonnelStatus;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PersonnelStatusController extends BaseController
{
    protected $status = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PersonnelStatus $status)
    {
        $this->middleware('auth:api');
        $this->status = $status;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $status = PersonnelStatus::orderBy('personnel_statuses.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($status, 'Personnel Status list');
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
        $status= new PersonnelStatus();
        $status->libelle=$request->get('libelle');
        $status->save();
        return $this->sendResponse($status, 'Personnel Status Created Successfully');
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
    
        $status = $this->status->findOrFail($id);
        // $status->update($request->all());

        $status->libelle=$request->get('libelle');
        $status->update();

        $log= new Log();
        $log->action='Modification Personnel Status';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($status, 'Personnel Status Information has been updated');
    }


    public function destroy($id)
    {
        $status = $this->status->findOrFail($id);
        $status->delete();

        $log= new Log();
        $log->action="Suppression de Personnel Status";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($status, 'Personnel Status has been Deleted');
    }
}
