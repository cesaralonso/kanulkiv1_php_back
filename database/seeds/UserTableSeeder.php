<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $role_admin = Role::where('name', 'admin')->first();
      $role_user = Role::where('name', 'user')->first();

      $user = new User();
      $user->email = 'admin@kanulki.com';
      $user->password = bcrypt('secret');
      $user->save();
      $user->roles()->attach($role_admin);

      // $user = new User();
      // $user->email = 'user@kanulki.com';
      // $user->password = bcrypt('secret');
      // $user->save();
      // $user->roles()->attach($role_user);
    }
}
