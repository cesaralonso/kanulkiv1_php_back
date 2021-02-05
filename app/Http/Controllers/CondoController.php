<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Condo;
use DB;

class CondoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $condos = Condo::all();

        return response()->json($condos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $condo = new Condo();

        $condo->name = $request->input('name');
        $condo->number_houses = $request->input('number_houses');
        $condo->address = $request->input('address');
        $condo->postal_code = $request->input('postal_code');
        $condo->description = $request->input('description');
        $condo->country_id = $request->input('country_id');
        $condo->state = $request->input('state');
        $condo->city = $request->input('city');
        $condo->image = $request->input('image');
        $condo->rfc = $request->input('rfc');
        $condo->legal_name = $request->input('legal_name');
        $condo->phone = $request->input('phone');
        $condo->email = $request->input('email');
        $condo->county = $request->input('county');
        $condo->assembly_doc = $request->input('assembly_doc');
        $condo->address_doc = $request->input('address_doc');
        $condo->rfc_doc = $request->input('rfc_doc');
        $condo->bank_statement = $request->input('bank_statement');
        $condo->club_house_gallery = $request->input('club_house_gallery');

        $condo->save();

        return response()->json(array('condo' => $condo), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condo = Condo::where('id', $id)->first();

        return response()->json($condo);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function totalCondos(){
        return response()->json(Condo::all()->count());
    }
}
