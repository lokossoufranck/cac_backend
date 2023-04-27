<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\Personnel;
use App\Models\Zeus\CompteBancaire;
use App\Models\Zeus\PersonnelPieceIdentite;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class PersonnelController extends BaseController
{
    protected $personnel = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Personnel $personnel)
    {
        $this->middleware('auth:api');
        $this->personnel = $personnel;
        $this->photo_dir_path = 'images/module/zeus/personnel_profile_photos/';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  

        // $latestBankAccount = CompteBancaire::select('personnel_id', DB::raw('MAX(created_at) as last_account_created_at'))
        //                                 ->groupBy('personnel_id');

        $latestId = CompteBancaire::select('personnel_id', DB::raw('MAX(created_at) as last_id_created_at'))
                                        ->groupBy('personnel_id');

        // $personnels = Personnel::select("personnels.*", 'compte_bancaires.numero as numero_de_compte', "compte_bancaires.banque_id", "personnel_piece_identites.numero as piece_identite_numero", "personnel_piece_identites.piece_identite_categorie_id as piece_identite_categorie_id", "personnel_piece_identites.date_expiration as piece_identite_date_expiration")
        //                         ->join('compte_bancaires', 'personnels.id', '=', 'compte_bancaires.personnel_id')
        //                         ->join('personnel_piece_identites', 'personnels.id', '=', 'personnel_piece_identites.personnel_id')
        //                         ->where('personnels.actif','=',true)
        //                         ->joinSub($latestBankAccount, 'latest_account', function ($join) {
        //                             $join->on('personnels.id', '=', 'latest_account.personnel_id')
        //                                 ->on("compte_bancaires.created_at", "=", "latest_account.last_account_created_at");
        //                         })
        //                         ->joinSub($latestId, 'latest_id', function ($join) {
        //                             $join->on('personnels.id', '=', 'latest_id.personnel_id')
        //                                 ->on("personnel_piece_identites.created_at", "=", "latest_id.last_id_created_at");
        //                         })
        //                         ->get();

        $personnels = Personnel::select("personnels.*", 'compte_bancaires.numero as numero_de_compte', "compte_bancaires.banque_id", "personnel_piece_identites.numero as piece_identite_numero", "personnel_piece_identites.piece_identite_categorie_id as piece_identite_categorie_id", "personnel_piece_identites.date_expiration as piece_identite_date_expiration")
                                ->join('compte_bancaires', 'personnels.id', '=', 'compte_bancaires.personnel_id')
                                ->join('personnel_piece_identites', 'personnels.id', '=', 'personnel_piece_identites.personnel_id')
                                ->where([
                                    ['personnels.actif', '=', true], 
                                    ['compte_bancaires.actif', '=', true], 
                                ])
                                // ->joinSub($latestId, 'latest_id', function ($join) {
                                //     $join->on('personnels.id', '=', 'latest_id.personnel_id')
                                //         ->on("personnel_piece_identites.created_at", "=", "latest_id.last_id_created_at");
                                // })
                                ->get();


        return $this->sendResponse($personnels, 'Personnel list');
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

        $matricule = (string) Str::uuid();

        $url_photo_identite = null;
        if($request->url_photo_identite){

            $dir_path = $this->photo_dir_path . $matricule;

            try {
                //code...
                mkdir($dir_path);
            } catch (\Throwable $th) {
                throw $th;
            }

            $name = time().'.'. explode('/', explode(':', substr($request->url_photo_identite, 0, strpos($request->url_photo_identite, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_identite=time().".". $extension;
            \Image::make($request->url_photo_identite)->save(public_path($dir_path)."/".$url_photo_identite);     
            // $url_photo_identite = $dir_path."/".$url_photo_identite;     
        }
      
        $personnel= new Personnel();
        $personnel->nom=$request->get('nom');
        $personnel->prenoms=$request->get('prenoms');
        $personnel->matricule= $matricule; // $request->get('matricule');  
        $personnel->numero_cnss=$request->get('numero_cnss');
        $personnel->nb_enfants_a_charge=$request->get('nb_enfants_a_charge');
        $personnel->date_naissance=$request->get('date_naissance');
        $personnel->domaine_etude=$request->get('domaine_etude');
        $personnel->adresse=$request->get('adresse');
        $personnel->telephone=$request->get('telephone');
        $personnel->date_entree=$request->get('date_entree');
        $personnel->situation_matrimoniale=$request->get('situation_matrimoniale');
        $personnel->sexe=$request->get('sexe');
        $personnel->email=$request->get('email');
        $personnel->service_id=$request->get('service_id');
        $personnel->nationalite_id=$request->get('nationalite_id');
        $personnel->etude_niveau_id=$request->get('etude_niveau_id');
        $personnel->status_id=$request->get('status_id');
        $personnel->fonction_id=$request->get('fonction_id');
        $personnel->permis_categorie_id=$request->get('permis_categorie_id');
        $personnel->domiciliation_irrevocable=$request->get('domiciliation_irrevocable');
        $personnel->personne_a_contacter=$request->get('personne_a_contacter');

        if ($url_photo_identite){
            $personnel->url_photo_identite = $url_photo_identite;
        }

        $compteBancaire = new CompteBancaire();
        $compteBancaire->numero = $request->get("numero_de_compte");
        $compteBancaire->banque_id = $request->get("banque_id");

        $pieceIdentite = new PersonnelPieceIdentite();
        $pieceIdentite->numero = $request->get("piece_identite_numero");
        $pieceIdentite->date_expiration = $request->get("piece_identite_date_expiration");
        $pieceIdentite->piece_identite_categorie_id = $request->get('piece_identite_categorie_id');

        $personnel->save();

        // saving compte bancaire object
        $compteBancaire->personnel_id = $personnel->id;
        $compteBancaire->save();

        $pieceIdentite->personnel_id = $personnel->id;
        $pieceIdentite->save();

        return $this->sendResponse($personnel, 'Personnel Created Successfully');
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
    
        $personnel = $this->personnel->findOrFail($id);
        // $personnel->update($request->all());

        # Photo
        $url_photo_identite = null;

        if($request->url_photo_identite){

            $dir_path = $this->photo_dir_path . $personnel->matricule;

            if (!file_exists($dir_path)){
                try {
                    //code...
                    mkdir($dir_path);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            $name = time().'.'. explode('/', explode(':', substr($request->url_photo_identite, 0, strpos($request->url_photo_identite, ';')))[1])[1];
            $extension=explode('.',  $name)[1];
            $url_photo_identite=time().".". $extension;
            \Image::make($request->url_photo_identite)->save(public_path($dir_path)."/".$url_photo_identite); 
        }

        $personnel->nom=$request->get('nom');
        $personnel->prenoms=$request->get('prenoms'); 
        $personnel->numero_cnss=$request->get('numero_cnss');
        $personnel->nb_enfants_a_charge=$request->get('nb_enfants_a_charge');
        $personnel->date_naissance=$request->get('date_naissance');
        $personnel->domaine_etude=$request->get('domaine_etude');
        $personnel->adresse=$request->get('adresse');
        $personnel->email=$request->get('email');
        $personnel->telephone=$request->get('telephone');
        $personnel->date_entree=$request->get('date_entree');
        $personnel->service_id=$request->get('service_id');
        $personnel->situation_matrimoniale=$request->get('situation_matrimoniale');
        $personnel->sexe=$request->get('sexe');
        $personnel->nationalite_id=$request->get('nationalite_id');
        $personnel->etude_niveau_id=$request->get('etude_niveau_id');
        $personnel->status_id=$request->get('status_id');
        $personnel->fonction_id=$request->get('fonction_id');
        $personnel->domiciliation_irrevocable=$request->get('domiciliation_irrevocable');
        $personnel->personne_a_contacter=$request->get('personne_a_contacter');
        $personnel->permis_categorie_id=$request->get('permis_categorie_id');
        $personnel->url_photo_identite = $url_photo_identite;

        $personnel->update();

        $banque_id = $request->get("banque_id ");
        $numero_de_compte = $request->get("numero_de_compte");

        // ->where([["numero", "=", $request->get("numero_de_compte")], ["banque_id", "=", $request->get("banque_id")]])
        $compteBancaire = CompteBancaire::orderBy('compte_bancaires.created_at','ASC')
                                        ->where([
                                            ["personnel_id", "=", $personnel->id],
                                            ["banque_id", "=", $banque_id],
                                            ["compte_bancaires.numero", "=", $numero_de_compte],
                                        ])
                                        ->first();
        
        if ($compteBancaire){

            // and setting the other to false
            $result = CompteBancaire::where([
                            ["personnel_id", "=", $personnel->id],
                            ["banque_id", "=", $banque_id],
                        ])
                        ->update(["actif", "=", false]);

            // setting the given bank account to "actif" status
            $compteBancaire->actif = true;
            $compteBancaire->update();
        }
        else{
            $compteBancaire = new CompteBancaire();
            $compteBancaire->numero = $request->get("numero_de_compte");
            $compteBancaire->banque_id = $request->get("banque_id"); 
            $compteBancaire->personnel_id = $personnel->id;
            $compteBancaire->save();
        }

        $pieceIdentite = PersonnelPieceIdentite::where([
            ["personnel_id", '=', $personnel->id],
            ["piece_identite_categorie_id", '=', $request->get('piece_identite_categorie_id')]
        ])
        ->first();

        if ($pieceIdentite){
            $pieceIdentite->numero = $request->get("piece_identite_numero");
            $pieceIdentite->date_expiration = $request->get("piece_identite_date_expiration");
        }
        else{
            $pieceIdentite = new PersonnelPieceIdentite();
            $pieceIdentite->numero = $request->get("piece_identite_numero");
            $pieceIdentite->date_expiration = $request->get("piece_identite_date_expiration");
            $pieceIdentite->personnel_id = $personnel->id;
            $pieceIdentite->piece_identite_categorie_id = $request->get('piece_identite_categorie_id');
        }

        $pieceIdentite->save();

        $log = new Log();
        $log->action='Modification Personnel';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($personnel, 'Personnel Information has been updated');
    }


    public function destroy($id)
    {
        $personnel = $this->personnel->findOrFail($id);
        $personnel->actif = false;
        $personnel->save();

        // $personnel->delete();

        $log= new Log();
        $log->action="Suppression de personnel";
        $log->enregistrement=$personnel->id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($personnel, 'Personnel has been Deleted');
    }
}
