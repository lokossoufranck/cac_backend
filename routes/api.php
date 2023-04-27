<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Foundation\Auth\EmailVerificationRequest;

 




Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
})->name('version');
/*
Route::get('/module/downloadsetting/{id}', function(){
    return response()->json(['version' => $id]);
});*/
Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::post('auth/register','UserController@register');
    Route::post('auth/signin','UserController@signin');
    Route::post('auth/logout','UserController@logout');
    Route::post('auth/profil','UserController@profil');
    Route::put('auth/update_profil/{id}','UserController@update_profil');  
    Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
    Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');
    Route::post('password/reset', 'UserController@reset');  
    Route::post('password/email', 'UserController@forgot'); 
});
 


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::get('profile', 'ProfileController@profile');
    Route::put('profile', 'ProfileController@updateProfile');
    Route::post('change-password', 'ProfileController@changePassword');
    Route::get('tag/list', 'TagController@list');
    Route::get('category/list', 'CategoryController@list');
    Route::post('product/upload', 'ProductController@upload');

    Route::apiResources([
        'user' => 'UserController',
        'product' => 'ProductController',
       // 'module' => 'ModuleController',
        'category' => 'CategoryController',
        'tag' => 'TagController',
    ]);
});


Route::namespace('App\\Http\\Controllers\\API\V1\\Module')->group(function () {
   // Route::get('test', 'PaysController@list');

    Route::post('module/uploadsetting', 'ModuleController@uploadsetting');
    Route::get('/module/downloadsetting/{module_id}', 'ModuleController@downloadsetting');
    Route::get('/module/list', 'ModuleController@list');
    Route::get('/module/list_by_user', 'ModuleController@list_by_user');


    Route::get('/pays/list', 'PaysController@list');
    Route::get('/client/list', 'ClientController@list');
    Route::get('/menu/list', 'MenuController@list');
    Route::get('/controllererp/list', 'ControllerController@list');
    Route::get('/fonctionnalite/list', 'FonctionnaliteController@list');
    Route::get('/profile/list', 'ProfileController@list');
    Route::get('/site/list', 'SiteController@list');
    Route::get('/profile_user/list', 'Profile_UserController@list');
    Route::get('/droit/list', 'Fonctionnalite_ProfileController@list');
    Route::get('/campagne/list', 'CampagneController@list');
    Route::get('/segment/list/{campagne_id}', 'SegmentController@list');
    Route::get('/departement/list', 'DepartementController@list');
    Route::get('/service/list', 'ServiceController@list');
    Route::get('/porte/list', 'PorteController@list');
    Route::post('/droit/change', 'Fonctionnalite_ProfileController@change');
    Route::get('/droit/list_off_droit', 'Fonctionnalite_ProfileController@list_off_droit');
    Route::apiResources([
        'pays' => 'PaysController',
        'client' => 'ClientController',
        'departement' => 'DepartementController',
        'module' => 'ModuleController',
        'menu' => 'MenuController',
        'sousmenu' => 'SousmenuController',
        'controllererp' => 'ControllerController',
        'fonctionnalite' => 'FonctionnaliteController',
        'profile' => 'ProfileController',
        'droit' => 'Fonctionnalite_ProfileController',
        'site' => 'SiteController',
        'profile_user' => 'Profile_UserController',
        'porte' => 'PorteController',
        'campagne' => 'CampagneController',
        'segment' => 'SegmentController',
        'service' => 'ServiceController',
    ]);
});



Route::namespace('App\\Http\\Controllers\\API\V1\\Event')->group(function () {
    Route::get('/evenement/list/', 'EvenementController@list');
    Route::get('/evenement/reporting/{date_debut}/{date_fin}', 'EvenementController@reporting');
    Route::get('/evenement/list/{campagne_id}/{porte_id}', 'DysfonctionnementController@list');
    Route::get('/detailsevenement/list/{evenement_id}', 'DetailsevenementController@list');
    Route::apiResources([
        'dysfonctionnement' => 'DysfonctionnementController',
        'evenement' => 'EvenementController',
        'detailsevenement' => 'DetailsevenementController',
      
        ]);
        
});


Route::namespace('App\\Http\\Controllers\\API\V1\\Zeus')->group(function () {
    // Route::get('/personnel/list', 'PersonnelController@list');
    Route::apiResources([
        'personnel' => 'PersonnelController',
        'conge' => 'CongeController',
        'conge_categorie' => 'CongeCategorieController',
        'compte_bancaire' => 'CompteBancaireController',
        'permis_categorie' => 'PermisCategorieController',
        'etude_niveau' => 'EtudeNiveauController',
        'nationalite' => 'NationaliteController',
        'banque' => 'BanqueController',
        'activite' => 'ActiviteController',
        'mode_paiement' => 'ModePaiementController',
        'absence' => 'AbsenceController',
        'mode_calcul' => 'ModeCalculController',
        'motif_absence' => 'MotifAbsenceController',
        'piece_identite_categorie' => 'PieceIdentiteCategorieController',
        'personnel_status' => 'PersonnelStatusController',
        'personnel_fonction' => 'PersonnelFonctionController',
        ]);
        
});


Route::namespace('App\\Http\\Controllers\\API\V1\\Formation')->group(function () {
    Route::get('formation/list', 'FormationController@list');
    Route::get('section/list', 'SectionController@list');
    Route::get('modulef/list', 'ModulefController@list');
    
    Route::apiResources([
        'formation' => 'FormationController',     
        ]);  
        Route::apiResources([
            'modulef' => 'ModulefController',     
            ]); 
        Route::apiResources([
                'section' => 'SectionController',     
                ]); 
        Route::apiResources([
                'lesson' => 'LessonController',     
                ]);    
});

Route::namespace('App\\Http\\Controllers\\API\V1\\Whatslearn')->group(function () {
    Route::get('whatslearn/list', 'WhatslearnController@list');
    Route::apiResources([
        'whatslearn' => 'WhatslearnController',     
        ]);    
});

Route::namespace('App\\Http\\Controllers\\API\V1\\Format')->group(function () {
    Route::get('/format/list', 'FormatController@list');
    Route::apiResources([
        'format' => 'FormatController',     
        ]);    
});

Route::namespace('App\\Http\\Controllers\\API\V1\\Ressource')->group(function () {
    Route::get('/ressource/download/{url}', 'RessourceController@download_ressource');
    Route::get('ressource/list', 'RessourceController@list');
    Route::apiResources([
        'ressource' => 'RessourceController',     
        ]);    
});

Route::namespace('App\\Http\\Controllers\\API\V1\\Web')->group(function () {
    Route::get('slider/list', 'SliderController@list');
    Route::get('page/list', 'PageController@list');
    Route::get('sectionh/list', 'SectionHController@list');
    Route::get('sectionv/list', 'SectionVController@list');

    Route::get('/slider/download/{url}', 'SliderController@download_slider');
    Route::get('/page/download/{url}', 'PageController@download_page');
    Route::get('/sectionh/download/{url}', 'SectionHController@download_sectionh');
    Route::get('/sectionv/download/{url}', 'SectionVController@download_sectionv');
   // Route::get('section/list', 'SectionController@list');
    //Route::get('modulef/list', 'ModulefController@list');

    Route::apiResources([
        'slider' => 'SliderController',   
        'page' => 'PageController', 
        'sectionh' => 'SectionHController',   
        'sectionv' => 'SectionVController',      
        ]);  

});










