<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\MotifAbsence;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MotifAbsenceController extends BaseController
{
    protected $motif = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MotifAbsence $motif)
    {
        // $this->middleware('auth:api');
        $this->motif = $motif;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $motifs = MotifAbsence::orderBy('motif_absences.id','ASC')
                                ->where('actif','=',true)
                                ->get()
                                // ->paginate(10, array('motif_absences.*'))
                                ;
        return $this->sendResponse($motifs, 'Motif Absence list');
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
        $motif= new MotifAbsence();
        $motif->libelle=$request->get('libelle');
        $motif->save();
        return $this->sendResponse($motif, 'Motif Absence Created Successfully');
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
    
        $motif = $this->motif->findOrFail($id);
        // $motif->update($request->all());

        $motif->libelle=$request->get('libelle');
        $motif->update();

        $log= new Log();
        $log->action='Modification Motif Absence';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($motif, 'Motifi Absence Information has been updated');
    }


    public function destroy($id)
    {
        $motif = $this->motif->findOrFail($id);
        $motif->actif = false;
        $motif->save();

        // $motif->delete();

        $log= new Log();
        $log->action="Suppression de Motif Absence";
        $log->enregistrement=$motif->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($motif, 'Motif Absence has been Deleted');
    }
}
