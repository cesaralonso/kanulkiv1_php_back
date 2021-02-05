<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TenantCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\TenantCharge;
use App\TenantPayment;
use App\Tenant;
use App\Condo;
use App\CondoFee;
use Carbon\Carbon;

class TenantChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenant_charges = TenantCharge::all();
        return response()->json(array('tenant_charges' => $tenant_charges, 200));

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
        $tenantCharge = new TenantCharge;
        $tenantCharge->tenant_id = $request->tenant_id;
        $tenantCharge->house_id = $request->house_id;
        $tenantCharge->description = $request->description;
        $tenantCharge->month = $request->month;
        $tenantCharge->year = $request->year;
        $tenantCharge->type = $request->type;
        $tenantCharge->amount = $request->amount;
        $tenantCharge->paid = 0;
        $tenantCharge->tenant_payment_id = null;
        
        if (isset($request->day)) {
            $tenantCharge->created_at = Carbon::create()->day($request->day)->month($request->month)->year($request->year);
        }

        $tenantCharge->save();

        $tenant = Tenant::find($request->tenant_id);
        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        //SEND MAIL

        



        return response()->json(array('tenant_charge' => $tenantCharge), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $tenantCharge = TenantCharge::find($id);
        
        $tenantCharge->tenant_id = $request->tenant_id;
        $tenantCharge->house_id = $request->house_id;
        $tenantCharge->description = $request->description;
        $tenantCharge->month = $request->month;
        $tenantCharge->year = $request->year;
        $tenantCharge->type = $request->type;
        $tenantCharge->amount = $request->amount;
        $tenantCharge->paid = 0;
        $tenantCharge->tenant_payment_id = null;
        if (isset($request->day)) {
            $tenantCharge->created_at = Carbon::create()->day($request->day)->month($request->month)->year($request->year);
        }
        $tenantCharge->save();

        $tenant = Tenant::find($request->tenant_id);
        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        return response()->json(array('tenant_charge' => $tenantCharge), 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $charge = TenantCharge::find($id);
        $tenant = Tenant::find($charge->tenant_id);
        TenantCharge::destroy($id);

        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        $message = 'Charge successfully deleted.';

        return response()->json(['message' => $message], 200);
    }

    public function maintenanceUpdate(){
        $condos = Condo::all();
        foreach ($condos as $condo) {
            $condo_fee = CondoFee::where('condo_id',$condo->id)->where('is_actual',1)->first();
            $tenants = Tenant::where('condo_id',$condo->id)->where('house_id','!=',0)->get();

            $date = Carbon::now()->locale('es');

            foreach ($tenants as $tenant) {
                $tenantCharge = TenantCharge::where('tenant_id',$tenant->id)->whereIn('month',[date('m'),intval(date('m'))])->where('year',date('Y'))->where('type',1)->first();
                if (is_null($tenantCharge)) {
                    $tenantCharge = new TenantCharge;
                    $tenantCharge->tenant_id = $tenant->id;
                    $tenantCharge->house_id = $tenant->house_id;
                    
                    //$tenantCharge->description = 'Mantenimiento junio';
                    //$tenantCharge->month = '06'; 
                    
                    $tenantCharge->description = 'Mantenimiento '.$date->isoFormat('MMMM');
                    $tenantCharge->month = date('m');
                    
                    $tenantCharge->year = date('Y');
                    $tenantCharge->type = 1;
                    $tenantCharge->amount = $condo_fee->maintenance;
                    
                    $tenantCharge->paid = 0;
                    //$tenantCharge->paid = rand(0,1);
                    
                    $tenantCharge->tenant_payment_id = null;
                    $tenantCharge->save();

                    $tenant->balance = $tenant->getBalance();
                    $tenant->save();
                }
            }
        }
    }

    public function interestsUpdate(){
        $condos = Condo::all();
        foreach ($condos as $condo) {
            $condo_fee = CondoFee::where('condo_id',$condo->id)->where('is_actual',1)->first();
            $tenants = Tenant::where('condo_id',$condo->id)->where('house_id','!=',0)->get();

            $date = Carbon::now()->locale('es');



            foreach ($tenants as $tenant) {
                $last_paid_maintenance = TenantCharge::where('tenant_id',$tenant->id)->where('type',1)->where('paid',1)->first();
                //$last_payment_month = Carbon::parse($last_paid_maintenance->)
                if (!is_null($last_paid_maintenance)) {
                    if (intval($last_paid_maintenance->month < intval(date('m')))) {
                        if ($condo_fee->revolving==0) {
                            $tenantCharge = new TenantCharge;
                            $tenantCharge->tenant_id = $tenant->id;
                            $tenantCharge->house_id = $tenant->house_id;
                            $tenantCharge->description = 'Intereses '.$date->isoFormat('MMMM');
                            $tenantCharge->month = date('m');
                            $tenantCharge->year = date('Y');
                            $tenantCharge->type = 4;
                            $tenantCharge->amount = $condo_fee->interest;
                            $tenantCharge->paid = 0;
                            $tenantCharge->tenant_payment_id = null;
                            $tenantCharge->save();

                            $tenant->balance = $tenant->getBalance();
                            $tenant->save();
                        }else{
                            $pastCharges = TenantCharge::where('tenant_id',$tenant->id)->where('paid',0)->where('type',4)->get();
                            foreach ($pastCharges as $charge) {
                                $charge->delete();
                            }

                            $tenantCharge = new TenantCharge;
                            $months = intval(date('m')) - intval($last_paid_maintenance->month);

                            $total_interests = (($months*($months+1))/2) * $condo_fee->interest;

                            $tenantCharge->tenant_id = $tenant->id;
                            $tenantCharge->house_id = $tenant->house_id;
                            $tenantCharge->description = 'Intereses '.$date->isoFormat('MMMM');
                            $tenantCharge->month = date('m');
                            $tenantCharge->year = date('Y');
                            $tenantCharge->type = 4;
                            $tenantCharge->amount = $total_interests;
                            $tenantCharge->paid = 0;
                            $tenantCharge->tenant_payment_id = null;
                            $tenantCharge->save();

                            $tenant->balance = $tenant->getBalance();
                            $tenant->save();
                        }
                    }
                }else{

                }
            }
        }   
    }

    public function chargesByTenantID($tenantId){
        $tenant_charges = TenantCharge::where('tenant_id',$tenantId)->get();

        return response()->json(array('tenant_charges' => $tenant_charges), 200);
    }

    public function chargesForPaymentUpdate($payment_id){
        $payment = TenantPayment::find($payment_id);
        $tenant_charges = TenantCharge::where('tenant_id',$payment->tenant_id)->where('paid',0)->orWhere('tenant_payment_id',$payment_id)->get();

        return response()->json(["tenant_charges"=>$tenant_charges],200);
    }

}
