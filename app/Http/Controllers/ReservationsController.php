<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reservation;
use DateTime;
use Carbon\Carbon;
use App\TenantCharge;
use App\Tenant;
use App\TenantPayment;
use Illuminate\Support\Facades\DB;

class ReservationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::all();
        return response()->json(array('reservations' => $reservations), 200);


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
        $existing_reservation = Reservation::where('date',$request->date)->where('condo_id',$request->condo_id)->where('turn',$request->turn)->where('approved',1)->first();

        if (!is_null($existing_reservation)) {
            return response()->json(['message' => 'Lo sentimos, esta fecha ya ha sido reservada'], 200);
        }
            $reservation = new Reservation;
            $reservation->tenant_id = $request->tenant_id;
            $reservation->condo_id = $request->condo_id;
            $reservation->date = $request->date;
            $reservation->turn = $request->turn;
            $reservation->cost = $request->cost;
            $reservation->deposit = $request->deposit;
            $reservation->refund = $request->refund;
            $reservation->event_type = $request->event_type;
            $reservation->number_of_people = $request->number_of_people;
            $reservation->approved = 0;
            $reservation->save();

            if ($request->turn == 1) {
                $turn = "mañana";
            }else{
                $turn = "noche";
            }

            $reservation_date = Carbon::create($request->date)->locale('es'); 
            $charge_date = Carbon::now()->locale('es');

            $tenantChargeApartado = new TenantCharge;
            $tenantChargeApartado->tenant_id = $request->tenant_id;
            $tenantChargeApartado->house_id = $reservation->id;
            $tenantChargeApartado->description = "Apartado Casa Club ".$reservation_date->isoFormat('DD')." de ".$reservation_date->isoFormat('MMMM')." de ".$reservation_date->isoFormat('YYYY')." por la ".$turn;
            $tenantChargeApartado->month = date('m');
            $tenantChargeApartado->year = date('Y');
            $tenantChargeApartado->type = 2;
            $tenantChargeApartado->amount = $request->cost;
            $tenantChargeApartado->paid = 0;
            $tenantChargeApartado->tenant_payment_id = null;

            $tenantChargeApartado->save();

            $tenantChargeFianza = new TenantCharge;
            $tenantChargeFianza->tenant_id = $request->tenant_id;
            $tenantChargeFianza->house_id = $reservation->id;
            $tenantChargeFianza->description = "Fianza Casa Club ".$reservation_date->isoFormat('DD')." de ".$reservation_date->isoFormat('MMMM')." de ".$reservation_date->isoFormat('YYYY')." por la ".$turn;
            $tenantChargeFianza->month = date('m');
            $tenantChargeFianza->year = date('Y');
            $tenantChargeFianza->type = 3;
            $tenantChargeFianza->amount = $request->deposit;
            $tenantChargeFianza->paid = 0;
            $tenantChargeFianza->tenant_payment_id = null;

            $tenantChargeFianza->save();


        return response()->json(array('reservation' => $reservation), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reservation = Reservation::find($id);

        return response()->json(['reservation'=>$reservation], 200);

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

    public function reservationsByCondoId($id)
    {
        $reservations = Reservation::where('condo_id',$id)->get();

        return response()->json(array('reservations' => $reservations), 200);

    }

    public function reservationsByTenantId($id)
    {
        $reservations = Reservation::where('tenant_id',$id)->get();

        return response()->json(array('reservations' => $reservations), 200);

    }

    public function reservationsByDate(Request $request){


        $reservations = Reservation::where('condo_id',$request->condo_id)->whereDate('date',$request->date)->get();

        return response()->json(array('reservations' => $reservations), 200);

    }

    public function reservationsByWeek(Request $request){

        $week = $this->getStartAndEndDate($request->year,$request->week);

        $from = date($week[0]);

        $to = date($week[1]);

        $reservations = Reservation::where('condo_id',$request->condo_id)->whereBetween('date',[$from,$to])->get();

        return response()->json(array('reservations' => $reservations), 200);
    }

    public function reservationsByMonth(Request $request){
        $month = $this->getStartAndEndDate($request->year,$request->month);

        $from = new DateTime($request->year."-".$request->month."-01");
 
        $to = $from->format('Y-m-t');

        $reservations = Reservation::where('condo_id',$request->condo_id)->whereBetween('date',[$from,$to])->get();

        return response()->json(array('reservations' => $reservations), 200);
    }

    function getStartAndEndDate($year, $week)
    {
       return [
          (new DateTime())->setISODate($year, $week)->format('Y-m-d'), //start date
          (new DateTime())->setISODate($year, $week, 7)->format('Y-m-d') //end date
       ];
    }

    function PendingChargesByReservation($reservation_id){
        $pending_charges = TenantCharge::whereIn('type', [2,3])->where('house_id',$reservation_id)->where('paid',0)->get();
        $charges=[];
        $total = 0;
        $description = "";
        foreach ($pending_charges as $charge) {
          array_push($charges, $charge);
          $total+=$charge->amount;
          $description = str_replace("Fianza", "Pago", $charge->description);
        }

        return response()->json(["charges"=>$charges,"total"=>$total,"description"=>$description]);
    }

    function PendingChargesByTentant($tenant_id){
        $reservations = Reservation::where('tenant_id',$tenant_id)->where('approved',0)->get();
        $pending_reservations = [];
        foreach ($reservations as $reservation) {
          $temp_array = [];
          $pending_charges = TenantCharge::whereIn('type', [2,3])->where('house_id',$reservation->id)->where('paid',0)->get();
          $charges=[];
          $total = 0;
          $description = "";
          foreach ($pending_charges as $charge) {
            array_push($charges, $charge);
            $total+=$charge->amount;
            $description = str_replace("Fianza", "Pago", $charge->description);
          }
          $temp_array["charges"] = $charges;
          $temp_array["total"] = $total;
          $temp_array["description"] = $description;
          $temp_array["reservation_id"] = $reservation->id;
          array_push($pending_reservations, $temp_array);
        }


        return $pending_reservations;
    }

    function PaymentByReservation($reservation_id){
        $charge = TenantCharge::whereIn('type', [2,3])->where('house_id',$reservation_id)->first();
        $payment = TenantPayment::find($charge->tenant_payment_id);

        return response()->json(['payment'=>$payment], 200);
    }

    function payMobile(Request $request){
        $reservation = Reservation::find($request->reservation_id);
        $charge_apartado = TenantCharge::where('type',2)->where('house_id',$reservation->id)->where('paid',0)->first();
        $charge_fianza = TenantCharge::where('type',3)->where('house_id',$reservation->id)->where('paid',0)->first();

        $tenant = Tenant::find($reservation->tenant_id);
        $user = $tenant->user;
        $customer_id = $user->conekta_customer_id;
        
        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');


        $customer = \Conekta\Customer::find($customer_id);


        try{
            $source = $customer->payment_sources[$request->card_index];
            $customer->update(["default_payment_source_id" => $source->id]);
        }catch (\Conekta\ProcessingError $error){
            return response()->json($error->getMessage());
        } catch (\Conekta\ParameterValidationError $error){
            return response()->json($error->getMessage());
        } catch (\Conekta\Handler $error){
            return response()->json($error->getMessage());
        }

        $line_items = [];
        $temp_array = [
            'name'=>$charge_apartado->description,
            'unit_price'=>intval($charge_apartado->amount) * 100,
            //'unit_price'=>150000,
            'quantity'=>1
        ];
        array_push($line_items, $temp_array);
        
        
        try{
            $order_reservation = \Conekta\Order::create(
                [
                  "line_items" => $line_items,
                  "currency" => "MXN",
                  "customer_info" => [
                    "customer_id" => $customer_id
                  ],
                  "metadata" => ["reservation_id" => $reservation->id],
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

            $line_items = [];
            $temp_array = [
                'name'=>$charge_fianza->description,
                'unit_price'=>intval($charge_fianza->amount) * 100,
                //'unit_price'=>130000,
                'quantity'=>1
            ];
            array_push($line_items, $temp_array);

            try{
                $order_deposit = \Conekta\Order::create(
                    [
                      "line_items" => $line_items,
                      "currency" => "MXN",
                      "customer_info" => [
                        "customer_id" => $customer_id
                      ],
                      "metadata" => ["reservation_id" => $reservation->id],
                      "charges" => [
                        [
                          "payment_method" => [
                            "type" => "default",
                          ]
                        ]
                      ],
                      "pre_authorize" => true
                    ]
                  );
            } catch (\Conekta\ProcessingError $error){
              return response()->json($error->getMessage());
            } catch (\Conekta\ParameterValidationError $error){
              return response()->json($error->getMessage());
            } catch (\Conekta\Handler $error){
              return response()->json($error->getMessage());
            }

        $tenantPaymentApartado = new TenantPayment;
        $tenantPaymentApartado->tenant_id = $reservation->tenant_id;
        $tenantPaymentApartado->payment_description = 0;
        $tenantPaymentApartado->amount = $reservation->cost;
        $tenantPaymentApartado->conekta_transaction_id = $order_reservation->id;
        $tenantPaymentApartado->payment_method = 4;
        $tenantPaymentApartado->save();

        $charge_apartado->tenant_payment_id = $tenantPaymentApartado->id;
        $charge_apartado->paid = 1;
        $charge_apartado->save();

        
        $tenantPaymentFianza = new TenantPayment;
        $tenantPaymentFianza->tenant_id = $reservation->tenant_id;
        $tenantPaymentFianza->payment_description = 0;
        $tenantPaymentFianza->amount = $reservation->deposit;
        $tenantPaymentFianza->conekta_transaction_id = $order_deposit->id;
        $tenantPaymentFianza->payment_method = 4;
        $tenantPaymentFianza->save();
        
        $charge_fianza->tenant_payment_id = $tenantPaymentFianza->id;
        $charge_fianza->paid = 1;
        $charge_fianza->save();

        $reservation->approved = 1;
        $reservation->save();


        return response()->json(['reservation' => $reservation, 'order_reservation'=>$order_reservation, 'order_deposit'=>$order_deposit], 200);
    }

    public function payWeb(Request $request){
        $reservation = Reservation::find($request->reservation_id);
        $charge_apartado = TenantCharge::where('type',2)->where('house_id',$reservation->id)->where('paid',0)->first();
        $charge_fianza = TenantCharge::where('type',3)->where('house_id',$reservation->id)->where('paid',0)->first();

        $tenantPaymentApartado = new TenantPayment;
        $tenantPaymentApartado->tenant_id = $reservation->tenant_id;
        $tenantPaymentApartado->payment_description = $request->payment_description;
        $tenantPaymentApartado->amount = $reservation->cost;
        $tenantPaymentApartado->conekta_transaction_id = 0;
        $tenantPaymentApartado->payment_method = $request->payment_method;
        $tenantPaymentApartado->save();

        $charge_apartado->tenant_payment_id = $tenantPaymentApartado->id;
        $charge_apartado->paid = 1;
        $charge_apartado->save();

        
        $tenantPaymentFianza = new TenantPayment;
        $tenantPaymentFianza->tenant_id = $reservation->tenant_id;
        $tenantPaymentFianza->payment_description = $request->payment_description;
        $tenantPaymentFianza->amount = $reservation->deposit;
        $tenantPaymentFianza->conekta_transaction_id = 0;
        $tenantPaymentFianza->payment_method = $request->payment_method;
        $tenantPaymentFianza->save();

        $charge_fianza->tenant_payment_id = $tenantPaymentFianza->id;
        $charge_fianza->paid = 1;
        $charge_fianza->save();

        $reservation->approved = 1;
        $reservation->save();

        return response()->json(['reservation'=>$reservation, 'payment_cost'=>$tenantPaymentApartado,'payment_deposit'=>$tenantPaymentFianza], 200);
    }
}
