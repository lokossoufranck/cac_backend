<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Absence;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AbsenceController extends BaseController
{
    protected $absence = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Absence $absence)
    {
        $this->middleware('auth:api');
        $this->absence = $absence;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $absences = Absence::orderBy('absences.created_at','ASC')
                                ->join('motif_absences', 'motif_absences.id', '=', 'absences.motif_id')
                                ->select("absences.*", "motif_absences.libelle as motif_libelle")
                                ->get()
                                // ->paginate(10, array('absences.*'))
                                ;
        return $this->sendResponse($absences, 'Absence list');
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
        $absence= new Absence();
        $absence->date_debut=$request->get('date_debut'); 
        $absence->date_fin=$request->get('date_fin'); 
        $absence->nombre_heures=$request->get('nombre_heures'); 
        $absence->motif_id=$request->get('motif_id'); 
        $absence->personnel_id=$request->get('personnel_id'); 
        $absence->save();
        return $this->sendResponse($absence, 'Absence Created Successfully');
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
    
        $absence = $this->absence->findOrFail($id);
        // $absence->update($request->all());

        $absence->date_debut=$request->get('date_debut'); 
        $absence->date_fin=$request->get('date_fin'); 
        $absence->nombre_heures=$request->get('nombre_heures'); 
        $absence->motif_id=$request->get('motif_id'); 
        $absence->personnel_id=$request->get('personnel_id'); 
        $absence->update();

        $log= new Log();
        $log->action='Modification Absence';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($absence, 'Absence Information has been updated');
    }


    public function destroy($id)
    {
        $absence = $this->absence->findOrFail($id);
        // $absence->actif = false;
        // $absence->save();

        $absence->delete();

        $log= new Log();
        $log->action="Suppression de absence";
        $log->enregistrement=$absence->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($absence, 'Absence has been Deleted');
    }
}
