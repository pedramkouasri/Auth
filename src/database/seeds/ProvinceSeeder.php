<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('provinces')->insert([
            ['title' => 'آذربايجان شرقي'],
            ['title' => 'آذربايجان غربي'],
            ['title' => 'اردبيل'],
            ['title' => 'اصفهان'],
            ['title' => 'البرز'],
            ['title' => 'ايلام'],
            ['title' => 'بوشهر'],
            ['title' => 'تهران'],
            ['title' => 'چهارمحال و بختياري'],
            ['title' => 'خراسان جنوبي'],
            ['title' => 'خراسان رضوي'],
            ['title' => 'خراسان شمالي'],
            ['title' => 'خوزستان'],
            ['title' => 'زنجان'],
            ['title' => 'سمنان'],
            ['title' => 'سيستان و بلوچستان'],
            ['title' => 'فارس'],
            ['title' => 'قزوين'],
            ['title' => 'قم'],
            ['title' => 'كردستان'],
            ['title' => 'كرمان'],
            ['title' => 'كرمانشاه'],
            ['title' => 'كهگيلويه و بويراحمد'],
            ['title' => 'گلستان'],
            ['title' => 'گيلان'],
            ['title' => 'لرستان'],
            ['title' => 'مازندران'],
            ['title' => 'مركزي'],
            ['title' => 'هرمزگان'],
            ['title' => 'همدان'],
            ['title' => 'يزد']
        ]);
    }
}
