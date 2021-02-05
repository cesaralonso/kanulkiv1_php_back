<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\TenantCharge;
use App\Tenant;
use App\Condo;
use App\CondoFee;
use Carbon\Carbon;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

        $condos = Condo::all();
        foreach ($condos as $condo) {
            $condo_fee = CondoFee::where('condo_id',$condo->id)->where('is_actual',1)->first();
            $tenants = Tenant::where('condo_id',$condo->id)->where('house_id','!=',0)->get();

            $date = Carbon::now()->locale('es');

            foreach ($tenants as $tenant) {
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
                $tenantCharge->save();

                $tenant->balance = $tenant->getBalance();
                $tenant->save();
            }
        }

        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
