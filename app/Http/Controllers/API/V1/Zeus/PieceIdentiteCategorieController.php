<?php

namespace App\Http\Controllers\API\V1\Zeus;
// use App\Http\Requests\Zeus\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Zeus\PieceIdentiteCategorie;
use App\Models\Module\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PieceIdentiteCategorieController extends BaseController
{
    protected $categorie = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PieceIdentiteCategorie $categorie)
    {
        // $this->middleware('auth:api');
        $this->categorie = $categorie;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        $categories = PieceIdentiteCategorie::orderBy('piece_identite_categories.created_at','ASC')
                                // ->paginate(10, array('banques.*'))
                                ->get()
                                ;

        return $this->sendResponse($categories, 'Piece Identite Categorie list');
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
        $categorie= new PieceIdentiteCategorie();
        $categorie->libelle=$request->get('libelle');
        $categorie->save();
        return $this->sendResponse($categorie, 'Piece identite Categorie Created Successfully');
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
    
        $categorie = $this->categorie->findOrFail($id);
        // $categorie->update($request->all());

        $categorie->libelle=$request->get('libelle');
        $categorie->update();

        $log= new Log();
        $log->action='Modification Piece Identite Categorie';
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();

        return $this->sendResponse($categorie, 'Piece Identite Categorie Information has been updated');
    }


    public function destroy($id)
    {
        $categorie = $this->categorie->findOrFail($id);
        $categorie->delete();

        $log= new Log();
        $log->action="Suppression de Piece Identite Categorie";
        $log->enregistrement=$id;
        $log->user_id=\Auth::user()->id;
        $log->save();
        return $this->sendResponse($categorie, 'Piece Identite Categorie has been Deleted');
    }
}
