<?php

namespace App\Http\Controllers\API\V1\Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\Module\Log;
use Illuminate\Http\Request;

class LogController extends BaseController
{
    protected $log = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Log $log)
    {
        $this->middleware('auth:api');
        $this->log = $log;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $log_s = $this->log->orderBy('action', 'ASC')->latest()->paginate(100);

        return $this->sendResponse($log_s, 'Log list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $log_s = $this->log->pluck('action', 'id');

        return $this->sendResponse($log_s, 'Log list');
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


        $this->validate($request, [
            'action' => 'required',
            'enregistrement' => 'required',
            'user_id' => 'required',
        ]);


        $log= new Log();
        $log->action=$request->get('action');
        $log->enregistrement=$request->get('enregistrement');
        $log->user_id=$request->get('user_id');
        $log->save();
        return $this->sendResponse($log, 'Log Created Successfully');
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
        $this->validate($request, [
            'action' => 'required',
            'enregistrement' => 'required',
            'user_id' => 'required',
        ]);
        $log = $this->log->findOrFail($id);
        $log->update($request->all());
        return $this->sendResponse($log, 'Log Information has been updated');
    }
}
