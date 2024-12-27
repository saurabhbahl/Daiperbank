<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'agency_id' => 1,
            'name' => 'Rebecca Cason',
            'email' => 'rcason@healthystepsdiaperbank.com',
            'username' => 'Rebecca',
            'password' => 'Hsdb0012!',
            'flag_active' => 1,
        ]);
    }
}
