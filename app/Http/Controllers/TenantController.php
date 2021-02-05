<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FileControl\FileControl;
use App\Mail\TenantCreated;
use Illuminate\Support\Facades\Mail;
use App\Tenant;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\TenantCharge;
use App\User;
use App\Role;
use DB;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = Tenant::all();
        // $tenants->users()->attach(User::where('id', $tenant->user_id)->first());

        return response()->json($tenants);
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
        $user = new User();
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        // $success['token'] = $user->createToken('kanulki')->accessToken;
        $user->save();

        $user->roles()->attach(Role::where('name', 'user')->first());
        $image_name="";
        if($request->hasFile('image')) {
            $fileName = FileControl::storeSingleFile($request->image, 'images_residents');
            $image_name = "/images_residents/{$fileName}";
        }

        if ($request->input('rfc')==null) {
            $rfc="";
        }else{
            $rfc=$request->input('rfc');
        }
        
        $tenant = new Tenant();

        $tenant->user_id = $user->id;
        $tenant->condo_id = $request->input('condo_id');
        $tenant->house_id = $request->input('house_id');
        $tenant->name = $request->input('name');
        $tenant->last_name = $request->input('last_name');
        $tenant->address = $request->input('address');
        $tenant->rfc = $rfc;
        $tenant->phone = $request->input('phone');
        $tenant->image = $image_name;
        $tenant->country_id = $request->input('country_id');
        
        $tenant->save();

        //SEND MAIL
        //Mail::to($request->input('email'))->send(new TenantCreated($tenant,$user));
        Mail::to('diegoc327.db@gmail.com')->send(new TenantCreated($tenant,$user));

        return response()->json(array('user' => $user, 'tenant' => $tenant), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $tenant = Tenant::where('id', $id)->first();

        // return response()->json($tenant);

        $tenant = DB::table('tenants')
                ->join('users', 'tenants.user_id', '=', 'users.id')
                ->join('houses', 'houses.id', '=', 'tenants.house_id')
                ->select('tenants.*', 'users.email', 'houses.name as house_name', 'houses.description as house_description')
                ->where('tenants.user_id', $id)
                ->get();

        return response()->json($tenant);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByTenantId($id)
    {
        // $tenant = Tenant::where('id', $id)->first();

        // return response()->json($tenant);

        $tenant = DB::table('tenants')
                ->join('users', 'tenants.user_id', '=', 'users.id')
                ->join('houses', 'houses.id', '=', 'tenants.house_id')
                ->select('tenants.*', 'users.email', 'houses.name as house_name', 'houses.description as house_description')
                ->where('tenants.id', $id)
                ->get();

        return response()->json($tenant);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBalanceTenant(Request $request)
    {
        $user_id = $request->input('user_id');
        $tenant = Tenant::where('user_id', $user_id)->first();

        $tenant->balance = $request->input('balance');

        $tenant->save();

        return response()->json($tenant);
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
     * Edit a created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   
        $user_id = $request->user_id;
        
        $user = User::where('id', $user_id)->get()->first();
        if($request->email != '' && $request->email != null) {
            $user->email = $request->input('email');
        }
        
        // $success['token'] = $user->createToken('kanulki')->accessToken;
        $user->save();

        $user->roles()->attach(Role::where('name', 'user')->first());
        
        $tenant = Tenant::where('user_id', $user_id)->get()->first();

        if($request->image != null) {
            $fileName = FileControl::storeSingleFile($request->image, 'images_residents');
            $image_name = "/images_residents/{$fileName}";
            $tenant->image = $image_name;
        }

        if($request->condo_id != '' && $request->condo_id != null) {
            $tenant->condo_id = $request->input('condo_id');
        }
        if($request->house_id != '' && $request->house_id != null) {
            $tenant->house_id = $request->input('house_id');
        }
        if($request->name != '' && $request->name != null) {
            $tenant->name = $request->input('name');
        }
        if($request->last_name != '' && $request->last_name != null) {
            $tenant->last_name = $request->input('last_name');
        }
        if($request->address != '' && $request->address != null) {
            $tenant->address = $request->input('address');
        }
        $tenant->rfc = $request->input('rfc');
        
        if($request->phone != '' && $request->phone != null) {
            $tenant->phone = $request->input('phone');
        }
        if($request->country_id != '' && $request->country_id != null) {
            $tenant->country_id = $request->input('country_id');
        }
        // $tenant->image = $image_name;
        $tenant->save();

        return response()->json(array('user' => $user, 'tenant' => $tenant), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    public function getBalance($id){
        $tenant = Tenant::find($id);
        return response()->json(array('balance' => $tenant->getBalance()), 200);
    }

    public function getBalanceDetail($id){
        $tenant = Tenant::find($id);
        $pending_charges = $tenant->getPendingCharges();
        $balance = $tenant->getBalance();
        return response()->json(array('balance' => $balance, 'pending_charges' => $pending_charges), 200);   
    }

    public function getBalanceDetailPending($id){
        $tenant = Tenant::find($id);
        $user = User::find($tenant->user_id);
        $pending_charges = $tenant->getPendingCharges();
        $balance = $tenant->getBalance();
        $date = Carbon:: now();
        $date = $date->isoFormat('DD')." de ".$date->isoFormat('MMMM')." de ".$date->isoFormat('YYYY');
        
        $pending_charges->name = $tenant->name." ".$tenant->last_name;
        $pending_charges->address = $tenant->address;
        $pending_charges->email = $user->email;
        $pending_charges->phone = $tenant->phone;
        $pending_charges->rfc = $tenant->rfc;
        $pending_charges->date = $date;
        $pending_charges->amount = $balance; 

        view()->share('pending_charges',$pending_charges);
        if ($tenant->condo_id == 1) {
            $pdf = PDF::loadView('pending_payment_pdf_sevilla', $pending_charges);    
        } else if ($tenant->condo_id == 2) {
            $pdf = PDF::loadView('pending_payment_pdf_plumbago', $pending_charges);
        } else {
            $pdf = PDF::loadView('pending_payment_pdf_sevilla', $pending_charges);
        }
      
      
    // download PDF file with download method
        return $pdf->download($pending_charges->name.'.pdf');
           
    }

    public function getDebtors(){
        //$debtors = Tenant::where('balance','>',0)->get();
        $debtors = DB::table('users')->join('tenants','users.id','=','tenants.user_id')
                    ->where('balance','>',0)
                    ->select('users.email','tenants.*')
                    ->get();
        return response()->json(array('debtors' => $debtors), 200);
    }

    public function getDebtorsTotal(){
        $total_debtors  = Tenant::where('balance','>',0)->get()->count();
        return response()->json(array('total_debtors' => $total_debtors), 200);
    }

    public function getDebtorsTotalByCondoID($condo_id){
        $total_debtors  = Tenant::where('balance','>',0)->where('condo_id',$condo_id)->get()->count();
        return response()->json(array('total_debtors' => $total_debtors), 200);
    }

    public function getDebtorsByCondoID($condo_id){
        //$debtors = Tenant::where('balance','>',0)->where('condo_id',$condo_id)->get();
        $debtors = DB::table('users')->join('tenants','users.id','=','tenants.user_id')
                    ->where('balance','>',0)->where('condo_id',$condo_id)
                    ->select('users.email','tenants.*')
                    ->get();
        return response()->json(array('debtors' => $debtors), 200);   
    }

    public function upToDateTenantsTotal(){
        $month_charges = TenantCharge::where('type',1)
                        ->where('paid',1)
                        ->where('month',date('m'))
                        ->get()
                        ->count();
        return response()->json(array('up_to_date_tenants',$month_charges));
    }

    public function upToDateTenantsByCondoID($condo_id){
        $month_charges = DB::table('tenants')
                        ->join('tenant_charges','tenants.id','=','tenant_charges.tenant_id')
                        ->where('type',1)
                        ->where('paid',1)
                        ->where('month',date('m'))
                        ->where('condo_id',$condo_id)
                        ->get()
                        ->count();
        return response()->json(array('up_to_date_tenants',$month_charges));
    }
}
