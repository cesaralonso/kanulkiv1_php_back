<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\HousePayment;
use App\TenantCharge;
use DB;

class HousePaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$house_payments = HousePayment::all();


        $house_payments = TenantCharge::where('type',1)->orderBy('house_id')->get();




        return response()->json(array('house_payments' => $house_payments), 200);
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
        $house_payment = new HousePayment();

        $house_payment->house_id = $request->input('house_id');
        $house_payment->year = $request->input('year');
        $house_payment->is_actual = $request->input('is_actual');
        $house_payment->m1 = $request->input('m1');
        $house_payment->m2 = $request->input('m2');
        $house_payment->m3 = $request->input('m3');
        $house_payment->m4 = $request->input('m4');
        $house_payment->m5 = $request->input('m5');
        $house_payment->m6 = $request->input('m6');
        $house_payment->m7 = $request->input('m7');
        $house_payment->m8 = $request->input('m8');
        $house_payment->m9 = $request->input('m9');
        $house_payment->m10 = $request->input('m10');
        $house_payment->m11 = $request->input('m11');
        $house_payment->m12 = $request->input('m12');

        $house_payment->save();

        return response()->json(array('house_payment' => $house_payment), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $house_payment = HousePayment::where('id', $id)->first();

        return response()->json($house_payment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showHousesPaymentsByHouseId($house_id)
    {
        /*$house_payment = HousePayment::where('house_id', $house_id)
                ->where('is_actual', 1)
                ->first();
        */

        $house_payments = TenantCharge::where('type',1)->where('house_id',$house_id)->get();

        return response()->json($house_payments);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showHousesPaymentsByCondoId($condo_id)
    {
        $houses = DB::table('houses')
                ->join('house_payments', 'house_payments.house_id', '=', 'houses.id')
                ->select('house_payments.*', 'houses.name as house_name', 'houses.description as house_description')
                ->where('houses.condo_id', $condo_id)
                ->get();

        return response()->json($houses);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showHousePaymentsByTenantId($user_id)
    {
        $houses = DB::table('houses')
                ->join('house_payments', 'house_payments.house_id', '=', 'houses.id')
                ->join('tenants', 'tenants.house_id', '=', 'houses.id')
                ->select('house_payments.*', 'houses.name as house_name', 'houses.description as house_description', 'tenants.*')
                ->where('tenants.user_id', $user_id)
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
        $house_payment = HousePayment::where('id', $id)->first();

        $house_payment->house_id = $request->input('house_id');
        $house_payment->year = $request->input('year');
        $house_payment->is_actual = $request->input('is_actual');
        $house_payment->m1 = $request->input('m1');
        $house_payment->m2 = $request->input('m2');
        $house_payment->m3 = $request->input('m3');
        $house_payment->m4 = $request->input('m4');
        $house_payment->m5 = $request->input('m5');
        $house_payment->m6 = $request->input('m6');
        $house_payment->m7 = $request->input('m7');
        $house_payment->m8 = $request->input('m8');
        $house_payment->m9 = $request->input('m9');
        $house_payment->m10 = $request->input('m10');
        $house_payment->m11 = $request->input('m11');
        $house_payment->m12 = $request->input('m12');
        
        $house_payment->save();
        
        return response()->json(array('house_payment' => $house_payment), 200);
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
