<?php

use Illuminate\Database\Seeder;

class AnswerTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\AnswerType::create([
            'type'=> 'Satu Jawaban Banyak Pilihan'
        ]);

        \App\AnswerType::create([
            'type'=> 'Banyak Jawaban Banyak Pilihan'
        ]);

        \App\AnswerType::create([
            'type'=> 'Angka'
        ]);

        \App\AnswerType::create([
            'type'=> 'Teks'
        ]);

    }
}
