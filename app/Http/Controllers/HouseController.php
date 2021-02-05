<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\House;
use DB;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $houses = House::all();

        return $houses;
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
        $house = new House();

        $house->name = $request->input('name');
        $house->description = $request->input('description');
        $house->condo_id = $request->input('condo_id');

        $house->save();

        return response()->json(array('house' => $house), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $house = House::where('id', $id)->first();

        return response()->json($house);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function housesByCondoID($condo_id)
    {
        /*
        $user = DB::table('houses')
                ->leftjoin('users', 'tenants.user_id', '=', 'users.id')
                ->leftjoin('tenants', 'tenants.house_id', '=', 'houses.id')
                ->select('tenants.*', 'users.email', 'houses.name as house_name', 'houses.description as house_description')
                ->where('houses.condo_id', $condo_id)
                ->get();

*/
 
        $houses = DB::table('houses')
//                ->leftjoin('users', 'tenants.user_id', '=', 'users.id')
                ->leftjoin('tenants', 'tenants.house_id', '=', 'houses.id')
                ->select('tenants.*', 'houses.name as house_name', 'houses.description as house_description')
                ->where('houses.condo_id', $condo_id)
                ->get();

        return response()->json($houses);
    }

    public function getAvailableHousesBycondoID($condo_id){
        $houses = DB::table('houses')
//                ->leftjoin('users', 'tenants.user_id', '=', 'users.id')
                ->leftjoin('tenants', 'tenants.house_id', '=', 'houses.id')
                ->select('tenants.id as tenant_id', 'houses.id as house_id', 'houses.name as house_name', 'houses.description as house_description')
                ->where('houses.condo_id', $condo_id)
                ->where('tenants.house_id',null)
                ->get();


        return response()->json($houses);
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
        $house = House::where('id', $id)->get()->first();
        
        $house->name = $request->input('name');
        $house->description = $request->input('description');
        $house->condo_id = $request->input('condo_id');

        $house->save();
        
        return response()->json(array('house' => $house), 200);
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
