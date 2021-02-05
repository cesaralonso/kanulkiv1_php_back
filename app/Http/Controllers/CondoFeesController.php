<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CondoFee;
use DB;

class CondoFeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $condo_fees = CondoFee::all();

        return response()->json($condo_fees);
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
        $condo_fee = new CondoFee();

        $condo_fee->year = $request->input('year');
        $condo_fee->is_actual = $request->input('is_actual');
        $condo_fee->maintenance = $request->input('maintenance');
        $condo_fee->interest = $request->input('interest');
        $condo_fee->club_house_morning = $request->input('club_house_morning');
        $condo_fee->club_house_evening = $request->input('club_house_evening');
        $condo_fee->condo_id = $request->input('condo_id');
        
        $condo_fee->save();

        return response()->json(array('condo_fee' => $condo_fee), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condo_fee = CondoFee::where('id', $id)->first();

        return response()->json($condo_fee);
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
        $condo_fee = CondoFee::where('id', $id)->get()->first();

        $condo_fee->year = $request->input('year');
        $condo_fee->is_actual = $request->input('is_actual');
        $condo_fee->maintenance = $request->input('maintenance');
        $condo_fee->interest = $request->input('interest');
        $condo_fee->club_house_morning = $request->input('club_house_morning');
        $condo_fee->club_house_evening = $request->input('club_house_evening');
        $condo_fee->condo_id = $request->input('condo_id');

        $condo_fee->save();

        return response()->json(array('condo_fee' => $condo_fee), 200);
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
}
