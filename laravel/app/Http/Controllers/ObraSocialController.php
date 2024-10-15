<?php

namespace App\Http\Controllers;

/* Models */
use App\Models\ObraSocial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ObraSocialFormRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class ObraSocialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('obra_social.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ObraSocialFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $obraSocial = ObraSocial::create([
                'descripcion'       =>  $request->descripcion,
                'numero_seguro'     =>  $request->numero_seguro,
                'user_created'      =>  1,
                'user_updated'      =>  1
            ]);
            if($obraSocial){
                DB::commit();
                return redirect('/');
            }

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       /*  $myValidator = Validator::make($request->all(),[
            'descripcion'   => 'required|between:4,10',
            'numero_seguro' => 'required'
        ]);
        if($myValidator->fails()){
            return redirect()->back()->withInput()->withErrors($myValidator);
        } */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
