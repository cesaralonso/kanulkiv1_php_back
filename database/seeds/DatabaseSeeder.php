<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      // RoleTableSeeder
      $this->call(RoleTableSeeder::class);
      // UserTableSeeder
      $this->call(UserTableSeeder::class);
      // CountriesTableSeeder
      $this->call(CountriesTableSeeder::class);
      // PaymentTypesTableSeeder
      $this->call(PaymentTypesTableSeeder::class);

      $this->call(PaymentMethodTableSeeder::class);
    }
}
