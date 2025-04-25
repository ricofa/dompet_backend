<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tips')->insert([
            [
                'title' => 'Strategi Memutarkan Uang Cerdas',
                'thumbnail' => 'nabung.jpg',
                'url' => 'https://www.cimbniaga.co.id/id/inspirasi/perencanaan/cara-menabung-yang-benar-menurut-pakar-keuangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Investasi Saham Modal Kecil',
                'thumbnail' => 'saham.jpg',
                'url' => 'https://www.brights.id/id/blog/cara-investasi-saham-modal-kecil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Emas sebagai Investasi Jangka Panjang',
                'thumbnail' => 'emas.jpg',
                'url' => 'https://sahabat.pegadaian.co.id/artikel/investasi/cara-investasi-emas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more tips as needed
        ]);
    }
}
