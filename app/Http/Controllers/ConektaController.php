<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Tenant;

require('../vendor/autoload.php');

class ConektaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // Test API Key
        \Conekta\Conekta::setApiKey(config('main.conekta_test'));
        \Conekta\Conekta::setLocale('es');

        // Prod API Key
        \Conekta\Conekta::setApiKey("");
        \Conekta\Conekta::setLocale('es');

        try{
            $thirty_days_from_now = (new \DateTime())->add(new \DateInterval('P30D'))->getTimestamp();

            $order = \Conekta\Order::create(
                [
                    "line_items" => [
                    [
                        "name" => "Pago en Oxxo",
                        "unit_price" => 100,
                        "quantity" => 1
                    ]
                ],
                "currency" => "MXN",
                "customer_info" => [
                    "name" => "Arihome User",
                    "email" => "user@airhome.com",
                    "phone" => "+5218181818181"
                ],
                    "charges" => [
                        [
                            "payment_method" => [
                                "type" => "oxxo_cash",
                                "expires_at" => $thirty_days_from_now
                            ]
                        ]
                    ]
                ]
            );

          dd($order);
        } catch (\Conekta\ParameterValidationError $error){
          $bug = $error->getMessage();
          dd($bug);
        } catch (\Conekta\Handler $error){
          $bug = $error->getMessage();
          dd($bug);
        }
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
        //
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

    public function addCustomer(Request $request)
    {
        // Test API Key
        

        // Prod API Key
        /*
        \Conekta\Conekta::setApiKey("");
        \Conekta\Conekta::setLocale('es');
        */
        $user = User::find($request->user_id);
        $tenant = Tenant::where('user_id',$request->user_id)->first();

        $tenant->condo_id == 1 ? $key=config('conekta_sevilla_private'): $key=config('conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        try{
            $customer = \Conekta\Customer::create(
                [
                    'name'  => $tenant->name,
                    'email' => $user->email,
                    'phone' => $request->phone,
                ]
            );

        }catch (\Conekta\ParameterValidationError $error){
            $bug = $error->getMessage();
            return response()->json(['bug' => $bug], 200);

        } catch (\Conekta\Handler $error){
            $bug = $error->getMessage();
            return response()->json(['bug' => $bug], 200);
        }

        $user->conekta_customer_id = $customer->id;
        $user->save();

        return response()->json(['customer' => $customer], 200);
    }

    public function addCard(Request $request){

        $user = User::find($request->user_id);
        $tenant = Tenant::where('user_id',$request->user_id)->first();

        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        if (!isset($user->conekta_customer_id)) {
            try{
              $customer = \Conekta\Customer::create(
                        [
                  'name'  => $tenant->name,
                  'email' => $user->email,
                  'phone' => $tenant->phone,
              ]
            );

              }catch (\Conekta\ParameterValidationError $error){
                  $bug = $error->getMessage();
                  return response()->json(['bug' => $bug], 200);

              } catch (\Conekta\Handler $error){
                  $bug = $error->getMessage();
                  return response()->json(['bug' => $bug], 200);
              }

              $user->conekta_customer_id = $customer->id;
              $user->save();
            }

        $customer = \Conekta\Customer::find($user->conekta_customer_id);

        $source = $customer->createPaymentSource([
          'token_id' => $request->conekta_token_id,
          'type'     => 'card'
        ]);
        return response()->json(['source' => $source], 200);
    }

    public function destroyCard(Request $request){

        $user = User::find($request->user_id);
        $tenant = Tenant::where('user_id',$request->user_id)->first();
        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        $customer = \Conekta\Customer::find($user->conekta_customer_id);

        $source = $customer->payment_sources[$request->source_index]->delete();

        $message = 'Card successfully deleted.';
        return response()->json(['message' => $message], 200);
    }

    public function getCardsByUser($id){
        $user = User::find($id);
        $tenant = Tenant::where('user_id',$id)->first();

        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private') : $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        if (!isset($user->conekta_customer_id)) {
          try{
              $customer = \Conekta\Customer::create([
                  'name'  => $tenant->name,
                  'email' => $user->email,
                  'phone' => $tenant->phone,
              ]);

              }catch (\Conekta\ParameterValidationError $error){
                  $bug = $error->getMessage();
                  return response()->json(['bug' => $bug], 200);

              } catch (\Conekta\Handler $error){
                  $bug = $error->getMessage();
                  return response()->json(['bug' => $bug], 200);
              }

              $user->conekta_customer_id = $customer->id;
              $user->save();
        }

        $customer = \Conekta\Customer::find($user->conekta_customer_id);
        $cards = json_decode($customer->payment_sources);

        return $cards;
    }

    public function getCardById(Request $request){

        $user = User::find($request->user_id);
        $tenant = Tenant::where('user_id',$id)->first();
        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        $customer = \Conekta\Customer::find($user->conekta_customer_id);

        return response()->json(['cards' => $customer->payment_sources], 200);
    }

    public function addPaymentMethod(Request $request){

        $user = User::find($request->user_id);

        $tenant = Tenant::where('user_id',$id)->first();
        $tenant->condo_id == 1 ? $key=config('main.conekta_sevilla_private'): $key=config('main.conekta_plumbago_private');
        \Conekta\Conekta::setApiKey($key);
        \Conekta\Conekta::setLocale('es');

        $customer = \Conekta\Customer::find($user->conekta_customer_id);

        $source = $customer->createPaymentSource([
          'token_id' => $request->conekta_token_id,
          'type'     => 'card',
        ]);

        return response()->json(['source' => $source], 200);
    }
}
