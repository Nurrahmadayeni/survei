<?php

use Illuminate\Database\Seeder;

class AuthsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Auth::create([
            'type'=> 'SU',
            'description' => 'Super User'
        ]);

        \App\Auth::create([
            'type'=> 'OPU',
            'description' => 'Operator Unit',
        ]);

        \App\Auth::create([
            'type'=> 'OPF',
            'description' => 'Operator Fakultas',
        ]);
    }
}