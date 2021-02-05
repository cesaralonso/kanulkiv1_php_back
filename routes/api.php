<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Auth::routes();
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::resource('users', 'UserController');
Route::get('users/condo/{condo_id}', 'UserController@usersByCondoID');
Route::get('users/detail/{user_id}', 'UserController@userDetailByUserID');
Route::get('users/count/total', 'UserController@totalUsers');
Route::get('users/count/condo/{condo_id}', 'UserController@totalUsersByCondoID');
Route::post('users/change_password','UserController@changePassword');


Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');
Route::post('password/email', 'ForgotPasswordController@forgot');
Route::post('password/reset', 'ForgotPasswordController@reset');

Route::resource('condos', 'CondoController');
Route::get('condos/count/total', 'CondoController@totalCondos');

Route::resource('condo_fees', 'CondoFeesController');

Route::post('tenants/update_tenant', 'TenantController@update');
Route::get('tenants/balance/{id}','TenantController@getBalance');
Route::get('tenants/balance/detail/{id}','TenantController@getBalanceDetail');
Route::get('tenants/balance/pdf/detail/{id}','TenantController@getBalanceDetailPending');
Route::get('tenants/debtors','TenantController@getDebtors');
Route::get('tenants/debtors/condo/{condo_id}','TenantController@getDebtorsByCondoID');
Route::get('tenants/debtors/count/total', 'TenantController@getDebtorsTotal');
Route::get('tenants/debtors/count/condo/{condo_id}', 'TenantController@getDebtorsTotalByCondoID');
Route::get('tenants/up_to_date/total','TenantController@upToDateTenantsTotal');
Route::get('tenants/up_to_date/condo/{condo_id}','TenantController@upToDateTenantsByCondoID');

Route::post('tenants/update_balance', 'TenantController@updateBalanceTenant');
Route::get('tenants/tenant/{id}', 'TenantController@showByTenantId');
Route::resource('tenants', 'TenantController');
Route::resource('countries', 'CountriesController');

Route::resource('houses', 'HouseController');
Route::get('houses/condo/{condo_id}', 'HouseController@housesByCondoID');
Route::get('houses/available/condo/{condo_id}','HouseController@getAvailableHousesBycondoID');

Route::resource('house_payments', 'HousePaymentsController');
Route::get('house_payments/houses/{house_id}', 'HousePaymentsController@showHousesPaymentsByHouseId');
Route::get('house_payments/condo/{condo_id}', 'HousePaymentsController@showHousesPaymentsByCondoId');
Route::get('house_payments/tenant/{user_id}', 'HousePaymentsController@showHousePaymentsByTenantId');

Route::resource('payment_types', 'PaymentTypesController');
Route::resource('tenant_payments','TenantPaymentsController');
Route::get('tenant_payments/tenant/{tenantId}','TenantPaymentsController@paymentsByTenantId');
Route::get('tenant_payments/pdf/{payment_id}','TenantPaymentsController@pdfByPaymentID');
Route::get('tenant_pending/pdf/{tenan_id}','TenantController@getBalanceDetailPending');
Route::get('tenant_payments/year_payment/pending_months/{tenant_id}','TenantPaymentsController@yearPaymentPendingMonths');
Route::post('tenant_payments/year_payment/pay/mobile','TenantPaymentsController@yearPaymentPayMobile');


Route::resource('payment_methods','PaymentMethodController');

Route::post('addPaymentMethod','ConektaController@addPaymentMethod');
Route::post('addCard','ConektaController@addCard');
Route::delete('destroyCard','ConektaController@destroyCard');
Route::get('getCardsByUser/{id}','ConektaController@getCardsByUser');
Route::post('addCustomer','ConektaController@addCustomer');

Route::resource('tenant_charges','TenantChargesController');
Route::get('tenant_charges/tenant/{tenant_id}','TenantChargesController@chargesByTenantID');
Route::get('tenant_charges/payment_update/{payment_id}','TenantChargesController@chargesForPaymentUpdate');
//Route::post('tenant_charges/update', 'TenantChargesController@update');

Route::resource('reservations','ReservationsController');
Route::post('reservations/date', 'ReservationsController@reservationsByDate');
Route::post('reservations/week', 'ReservationsController@reservationsByWeek');
Route::post('reservations/month', 'ReservationsController@reservationsByMonth');
Route::get('reservations/pending_charges/{reservation_id}','ReservationsController@PendingChargesByReservation');
Route::get('reservations/pending_charges/tenant/{tenant_id}','ReservationsController@PendingChargesByTentant');


Route::get('reservations/payment/{reservation_id}','ReservationsController@PaymentByReservation');
Route::post('reservations/pay/mobile','ReservationsController@payMobile');
Route::post('reservations/pay/web','ReservationsController@payWeb');
Route::get('reservations/condo/{condo_id}', 'ReservationsController@reservationsByCondoId');
Route::get('reservations/tenant/{tenant_id}', 'ReservationsController@reservationsByTenantId');

Route::resource('messages','MessagesController');
Route::get('messages/list/unread','MessagesController@getUnreadMessages');
Route::get('messages/list/read','MessagesController@getReadMessages');
Route::get('messages/read/condo/{condo_id}','MessagesController@readMessagesByCondoId');
Route::get('messages/unread/condo/{condo_id}','MessagesController@unreadMessagesByCondoId');
Route::get('messages/condo/{condo_id}','MessagesController@messagesByCondoId');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
