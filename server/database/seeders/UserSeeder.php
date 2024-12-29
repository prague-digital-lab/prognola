<?php

namespace Database\Seeders;

use App\Models\User;
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
        //        $user = new User();
        //        $user->name = "Michael Dojčár";
        //        $user->email = 'michael.dojcar@valasskyboyard.cz';
        //        $user->password = bcrypt('fortline321**515');
        //        $user->save();

        //        $user = new User();
        //        $user->name = "Jan Lušovský";
        //        $user->email = 'jan.lusovsky@valasskyboyard.cz';
        //        $user->password = bcrypt('fort-321**582!-AxB');
        //        $user->save();

        //        $user = new User();
        //        $user->name = "Martin Svák";
        //        $user->email = 'martin@digament.cz';
        //        $user->password = bcrypt('fort-321**51290-AxB');
        //        $user->save();

        //        $user           = new User();
        //        $user->name     = "Hana Sváková";
        //        $user->email    = 'hana.svakova@gmail.com';
        //        $user->password = bcrypt('fort-321*938361-726');
        //        $user->save();

        //        $user           = new User();
        //        $user->name     = "František Fůsek";
        //        $user->email    = 'funny.cz14@seznam.cz';
        //        $user->password = bcrypt('fort-321*938361-8272');
        //        $user->save();

        $user = new User;
        $user->name = 'Petr Bíl';
        $user->email = 'petrbil@post.cz';
        $user->password = bcrypt('fort-321*382926-124');
        $user->save();

        //        $user           = new User();
        //        $user->name     = "Martin Svák";
        //        $user->email    = 'admin@admin.com';
        //        $user->password = bcrypt('secret');
        //        $user->save();
    }
}
