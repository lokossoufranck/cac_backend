<?php

namespace App\Http\Controllers\API\V1\Event;
use App\Http\Requests\Event\EvenementRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Event\Evenement;
use App\Models\Event\Detailsevenement;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;

class EvenementController extends BaseController
{
    protected $evenement = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Evenement $evenement)
    {
        $this->middleware('auth:api');
        $this->evenement = $evenement;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $evenements = Evenement::orderBy('evenements.date_debut','DESC')
        ->paginate(10, array('evenements.*'));
        return $this->sendResponse($evenements, 'Evenement list');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $evenements = $this->evenement->pluck('date_debut', 'id');    
        return $this->sendResponse($evenements, 'Evenement list');
    }

    public function reporting(Request $request,$date_debut,$date_fin){
        /*$evenements = Evenement::join('detailsevenements','detailsevenements.evenement_id','evenements.id')
        ->get();*/
        $date_debut = new DateTime($date_debut);
        $date_fin = new DateTime($date_fin);


        $evenements = Detailsevenement::join('segments','segments.id','detailsevenements.segment_id')
        ->join('evenements','evenements.id','detailsevenements.evenement_id')
        ->join('dysfonctionnements','dysfonctionnements.id','detailsevenements.dysfonctionnement_id')
        -> where('date_debut','>',$date_debut)
        -> where('date_debut','<',$date_fin)
        ->get(
            ['detailsevenements.*',
            'segments.nom AS segment_nom',
             'dysfonctionnements.nom AS dysfonctionnement_nom',
             'dysfonctionnements.porte_id',
             'dysfonctionnements.campagne_id',
             'date_debut',
             'date_fin',
             
             ]
            );
        // Récupération des segments concernées
        $segments = $evenements->unique('segment_id');
        $spreadsheet = new Spreadsheet();

      

      
        $i=0;
        foreach( $segments  as $key=>$segment){ // for each segment
              $days_numbers = [
           
                0 => 'Samedi',
                1 => 'Lundi',
                2 => 'Mardi',
                3 => 'Mercredi',
                4 => 'Jeudi',
                5 => 'Vendredi',
                6 => 'Dimanche',
               ];
            $segment_id=$segment->segment_id;
            $myWorkSheet = new Worksheet($spreadsheet, 'SEG_'.$segment->segment_nom);
           
                $myWorkSheet->setCellValue('B1', "MATRICE DES EVENEMENTS"); 
                $myWorkSheet->setCellValue('A6', "JOUR"); 
                $myWorkSheet->setCellValue('B6', "DATE");
                $myWorkSheet->setCellValue('C6', "EVENEMENTS");
                $myWorkSheet->setCellValue('D6', "VOLUME PREVU");
                $myWorkSheet->setCellValue('E6', "VOLUME REALISE");
                $myWorkSheet->setCellValue('F6', "TRP");                    
            

            $detailsevenements= $evenements->where('segment_id',$segment_id);
            $l=7;
            foreach( $detailsevenements as $key=>$details){ // for each row
               // $dayOfTheWeek = Carbon::now()->dayOfWeek;
               $date=$details->date_debut;
               $dayofweek = date('w', strtotime($date));

                $myWorkSheet->setCellValue('A'.($l),  $days_numbers [$dayofweek]);
                $myWorkSheet->setCellValue('B'.($l),  $details->date_debut);
                $myWorkSheet->setCellValue('C'.($l),  $details->dysfonctionnement_nom);
                $myWorkSheet->setCellValue('D'.($l),  $details->volume_prevu);
                $myWorkSheet->setCellValue('E'.($l),  $details->volume_realise);
                $myWorkSheet->setCellValue('F'.($l),  "=E$l/D$l");
                $l=$l+1;
            }

            foreach (range('A','O') as $col) {
                $myWorkSheet->getColumnDimension($col)->setAutoSize(true);
             }
            $spreadsheet->addSheet($myWorkSheet,  $i);
            $i++;

        }

        $writer = new Xlsx($spreadsheet);
       // $path='files/event/MATRICE_DES_EVENEMENTS.xlsx';
        $path = public_path('files/event/MATRICE_DES_EVENEMENTS.xlsx'); 
        $writer->save($path);
        return response()->download($path);
       // return $this->sendResponse($segments, 'Reporting');
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
    public function store(EvenementRequest $request)
    {
      
        $evenement= new Evenement();
        $evenement->date_debut=$request->get('date_debut');
        $evenement->date_fin=$request->get('date_fin');  
        $evenement->nb_agent_impacte=$request->get('nb_agent_impacte');  
        $evenement->details=$request->get('details');
        $evenement->save();
        return $this->sendResponse($evenement, 'Evenement Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(EvenementRequest $request, $id)
    
    {
    
        $evenement = $this->evenement->findOrFail($id);
      
        $evenement->update($request->all());

        $log= new Log();
        $log->action='Modification Evenement';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
 return response()->download($path);
        return $this->sendResponse($evenement, 'Evenement Information has been updated');
    }


    public function destroy($id)
    {
        $evenement = $this->evenement->findOrFail($id);
        $evenement->delete();

        $log= new Log();
        $log->action="Suppression de evenement";
        $log->enregistrement=$evenement->id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($evenement, 'evenement has been Deleted');
    }
}
