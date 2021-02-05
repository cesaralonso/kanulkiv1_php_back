<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\PaymentCreated;
use Illuminate\Support\Facades\Mail;
use App\TenantPayment;
use App\TenantCharge;
use App\Tenant;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use App\User;
use File;
use Storage;
use App\CondoFee;

class TenantPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenant_payments = TenantPayment::all();

        return $tenant_payments;
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
        $charges_ids = json_decode($request->tenant_charges_ids);
        $order = "";
        if ($request->payment_method == 4) {
        
            $tenant = Tenant::find($request->tenant_id); 
            $user = $tenant->user;

            $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
            \Conekta\Conekta::setApiKey($key);
            \Conekta\Conekta::setLocale('es');

            $customer_id = $user->conekta_customer_id;
            $customer = \Conekta\Customer::find($customer_id);
            if ($request->card_index != "") {
                $source = $customer->payment_sources[$request->card_index];
                $customer->update(["default_payment_source_id" => $source->id]);
            }

            $line_items = [];
            foreach ($charges_ids as $charge_id) {
                $charge=TenantCharge::find($charge_id->id);
                $temp_array = [
                    'name' => $charge->description,
                    'unit_price' => $charge->amount *100,
                    'quantity' => 1,
                ];
                array_push($line_items, $temp_array);
            }

            try{
                  $order = \Conekta\Order::create(
                    [
                      "line_items" => $line_items,
                      "currency" => "MXN",
                      "customer_info" => [
                        "customer_id" => $customer_id
                      ],
                      "metadata" => ["reference" => $request->tenant_id],
                      "charges" => [
                        [
                          "payment_method" => [
                            "type" => "default",
                          ]
                        ]
                      ]
                    ]
                  );
                } catch (\Conekta\ProcessingError $error){
                  return response()->json($error->getMessage());
                } catch (\Conekta\ParameterValidationError $error){
                  return response()->json($error->getMessage());
                } catch (\Conekta\Handler $error){
                  return response()->json($error->getMessage());
                }
        }

        $date = Carbon::create()->day($request->day)->month($request->month)->year($request->year);
        
        $tenantPayment = new TenantPayment; 
        $tenantPayment->tenant_id = $request->tenant_id;
        $tenantPayment->payment_description = $request->payment_description;
        $tenantPayment->amount = $request->amount;
        $tenantPayment->conekta_transaction_id = 0;
        $tenantPayment->payment_method = $request->payment_method;
        if (isset($request->day)) {
            $tenantPayment->created_at = $date;
        }

        //$location = "/contpaq_invoices/facturas";
        $fileName = $request->payment_description;
        if($request->hasFile('invoice_pdf')) {
            Storage::disk('invoices')->put($fileName.".pdf", File::get($request->invoice_pdf));
        }

        if($request->hasFile('invoice_xml')) {
            Storage::disk('invoices')->put($fileName.".xml", File::get($request->invoice_xml));
        }

        //Storage::disk($location)->put($fileName, File::get($file));
        if ($request->payment_method == 4) {
            $tenantPayment->conekta_transaction_id = $order->id;
        }

        $tenantPayment->save();

        $tenantPayment = TenantPayment::find($tenantPayment->id);

        $charges_array = [];
        
        foreach ($charges_ids as $charge_id) {
            $charge=TenantCharge::find($charge_id->id);
            $charge->paid = 1;
            $charge->tenant_payment_id = $tenantPayment->id;
            $charge->save();
            array_push($charges_array, $charge);
        }

        $tenant = Tenant::find($request->tenant_id);
        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        $date = $date->isoFormat('DD')." de ".$date->isoFormat('MMMM')." de ".$date->isoFormat('YYYY');

        //Mail::to($tenant->user->email)->send(new PaymentCreated($tenant,$tenantPayment,$date));
        Mail::to('diegoc327.db@gmail.com')->send(new PaymentCreated($tenant,$tenantPayment,$date));

        return response()->json(array('tenant_payment' => $tenantPayment, 'conekta_order' => $order, $charges_array), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenant_payment = TenantPayment::find($id);
        $tenant_payment->charges;
        
        return response()->json(['tenant_payment' => $tenant_payment]);
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
        $date = Carbon::create()->day($request->day)->month($request->month)->year($request->year);
        $charges_ids = json_decode($request->tenant_charges_ids);
        $tenantPayment = TenantPayment::find($id); 
        $tenantPayment->payment_description = $request->payment_description;
        $tenantPayment->amount = $request->amount;

        $tenantPayment->payment_method = $request->payment_method;
        if (isset($request->day)) {
            $tenantPayment->created_at = $date;
        }

        //$location = "/contpaq_invoices/facturas";
        $fileName = $request->payment_description;
        if($request->hasFile('invoice_pdf')) {
            Storage::disk('invoices')->put($fileName.".pdf", File::get($request->invoice_pdf));
        }

        if($request->hasFile('invoice_xml')) {
            Storage::disk('invoices')->put($fileName.".xml", File::get($request->invoice_pdf));
        }

        //Storage::disk($location)->put($fileName, File::get($file));

        $tenantPayment->save();

        $tenantPayment = TenantPayment::find($tenantPayment->id);

        foreach ($tenantPayment->charges as $charge) {
            $charge->paid=0;
            $charge->tenant_payment_id = null;
            $charge->save();
        }

        $charges_array = [];
        
        foreach ($charges_ids as $charge_id) {
            $charge=TenantCharge::find($charge_id->id);
            $charge->paid = 1;
            $charge->tenant_payment_id = $tenantPayment->id;
            $charge->save();
            array_push($charges_array, $charge);
        }

        $tenant = Tenant::find($request->tenant_id);
        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        return response()->json(array('tenant_payment' => $tenantPayment, $charges_array), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)

    {
        $payment = TenantPayment::find($id);
        $tenant = Tenant::find($payment->tenant_id);        
        foreach ($payment->charges as $charge) {
            $charge->paid=0;
            $charge->tenant_payment_id = null;
            $charge->save();
        }
        TenantPayment::destroy($id);

        $tenant->balance = $tenant->getBalance();
        $tenant->save();

        $message = 'Payment successfully deleted.';

        return response()->json(['message' => $message], 200);
    }

    public function paymentsByTenantId($tenantId){
        $payments = TenantPayment::where('tenant_id',$tenantId)->get();

        foreach ($payments as $payment) {
            $payment->charges;
        }

        return response()->json(array('payments' => $payments), 200);

    }

    public function pdfByPaymentID($payment_id) {
      
        $payment = TenantPayment::find($payment_id);
        $date = Carbon::parse($payment->created_at);
        $date = $date->isoFormat('DD')." de ".$date->isoFormat('MMMM')." de ".$date->isoFormat('YYYY');
        $payment->date = $date;

        $tenant = Tenant::find($payment->tenant_id);
        $user = User::find($tenant->user_id);

        $payment->name = $tenant->name." ".$tenant->last_name;
        $payment->email = $user->email;
        $payment->phone = $tenant->phone;
        $payment->rfc = $tenant->rfc;

      // share data to view
        view()->share('payment',$payment);
        if ($tenant->condo_id == 1) {
            $pdf = PDF::loadView('payment_pdf_sevilla', $payment);    
        }elseif ($tenant->condo_id == 2) {
            $pdf = PDF::loadView('payment_pdf_plumbago', $payment);
        }else{
            $pdf = PDF::loadView('payment_pdf_sevilla', $payment);
        }
        

      // download PDF file with download method
      return $pdf->download($payment->payment_description.'.pdf');
    }

    public function pdfByPendingPaymentID($tenan_id) {
      $payment = TenantPayment::find($tenan_id);
      $date = Carbon::parse($payment->created_at);
      $date = $date->isoFormat('DD')." de ".$date->isoFormat('MMMM')." de ".$date->isoFormat('YYYY');
      $payment->date = $date;
      
      $tenant = Tenant::find($payment->tenant_id);
      $user = User::find($tenant->user_id);

      $payment->name = $tenant->name." ".$tenant->last_name;
      $payment->email = $user->email;
      $payment->phone = $tenant->phone;
      $payment->rfc = $tenant->rfc;
    // share data to view
      view()->share('payment',$payment);
      if ($tenant->condo_id == 1) {
          $pdf = PDF::loadView('pending_payment_pdf_sevilla', $payment);    
      }elseif ($tenant->condo_id == 2) {
          $pdf = PDF::loadView('pending_payment_pdf_plumbago', $payment);
      }else{
          $pdf = PDF::loadView('pending_payment_pdf_sevilla', $payment);
      }
      
      
    // download PDF file with download method
    return $pdf->download($payment->payment_description.'.pdf');
  }

    public function yearPaymentPayMobile(Request $request){
      $tenant = Tenant::find($request->tenant_id); 
      $user = $tenant->user;


      $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
      \Conekta\Conekta::setApiKey($key);
      \Conekta\Conekta::setLocale('es');

      $condo_fee = CondoFee::where('condo_id',$tenant->condo_id)->where('is_actual',1)->first();
      $date = Carbon::now()->locale('es');

      $charges=[];

      for ($i=1; $i <=12 ; $i++) {
        $tenantCharge = TenantCharge::where('tenant_id',$tenant->id)->where('month',$i)->where('year',date('Y'))->where('type',1)->first();
          $date = Carbon::create()->month($i)->locale('es');
        if (is_null($tenantCharge)) {
          $tenantCharge = new TenantCharge;
          $tenantCharge->tenant_id = $tenant->id;
          $tenantCharge->house_id = $tenant->house_id;
          $tenantCharge->description = 'Mantenimiento '.$date->isoFormat('MMMM');
          $tenantCharge->month = $i;
          $tenantCharge->year = date('Y');
          $tenantCharge->type = 1;
          $tenantCharge->amount = $condo_fee->maintenance;
          $tenantCharge->paid = 0;                
          $tenantCharge->tenant_payment_id = null;
        }
        if ($tenantCharge->paid == 0) {
          array_push($charges, $tenantCharge); 
        }      }

      $customer_id = $user->conekta_customer_id;
      $customer = \Conekta\Customer::find($customer_id);
      if ($request->card_index != "") {
          $source = $customer->payment_sources[$request->card_index];
          $customer->update(["default_payment_source_id" => $source->id]);
      }

      $line_items = [];

      foreach ($charges as $charge) {
        $temp_array = [
            'name' => $charge->description,
            'unit_price' => $charge->amount *100,
            'quantity' => 1,
        ];
        array_push($line_items, $temp_array);
      }

      try{
          $order = \Conekta\Order::create(
            [
              "line_items" => $line_items,
              "currency" => "MXN",
              "customer_info" => [
                "customer_id" => $customer_id
              ],
              "metadata" => ["reference" => $tenant->id],
              "charges" => [
                [
                  "payment_method" => [
                    "type" => "default",
                  ]
                ]
              ]
            ]
          );
        } catch (\Conekta\ProcessingError $error){
          return response()->json($error->getMessage());
        } catch (\Conekta\ParameterValidationError $error){
          return response()->json($error->getMessage());
        } catch (\Conekta\Handler $error){
          return response()->json($error->getMessage());
        }

        $tenantPayment = new TenantPayment; 
        $tenantPayment->tenant_id = $tenant->id;
        $tenantPayment->payment_description = "";
        $tenantPayment->amount = $order->amount/100;
        $tenantPayment->conekta_transaction_id = $order->id;
        $tenantPayment->payment_method = 4;
        $tenantPayment->save();

        foreach ($charges as $charge) {
          $charge->tenant_payment_id = $tenantPayment->id;
          $charge->paid = 1;
          $charge->save();
        }
        $tenant->balance = $tenant->getBalance();
        $tenant->save();

      return response()->json(["payment"=>$tenantPayment,"conekta_order"=>$order],200);

    }

    public function yearPaymentPendingMonths($tenant_id){
      $tenant = Tenant::find($tenant_id); 
      $user = $tenant->user;

      $total = 0;
      
      $condo_fee = CondoFee::where('condo_id',$tenant->condo_id)->where('is_actual',1)->first();
      $date = Carbon::now()->locale('es');

      $charges=[];

      for ($i=1; $i <=12 ; $i++) {
        $tenantCharge = TenantCharge::where('tenant_id',$tenant->id)->where('month',$i)->where('year',date('Y'))->where('type',1)->first();
        $date = Carbon::create()->month($i)->locale('es');
        if (is_null($tenantCharge)) {
          $tenantCharge = new TenantCharge;
          $tenantCharge->tenant_id = $tenant->id;
          $tenantCharge->house_id = $tenant->house_id;
          $tenantCharge->description = 'Mantenimiento '.$date->isoFormat('MMMM');
          $tenantCharge->month = date('m');
          $tenantCharge->year = date('Y');
          $tenantCharge->type = 1;
          $tenantCharge->amount = $condo_fee->maintenance;
          $tenantCharge->paid = 0;                
          $tenantCharge->tenant_payment_id = null;
        }
        if ($tenantCharge->paid == 0) {
          array_push($charges, $tenantCharge); 
          $total += $tenantCharge->amount;
        }
      }

      return response()->json(["total"=>$total,"pending_months"=>$charges],200);
    }
}
