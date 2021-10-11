<?php
ini_set('memory_limit', '512M');

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
        /**
         * Cities
         */
        \Illuminate\Support\Facades\DB::table('cities')->delete();
        $path = __DIR__.DIRECTORY_SEPARATOR.'city.list.min.json';
        $citiesInsert = array();
        foreach (json_decode(file_get_contents($path)) as $city){
            $newCity = array(
                'api_id' => $city->id,
                'name' => $city->name,
                'country_code' => $city->country,
                'coord_lat' => $city->coord->lat,
                'coord_lon' => $city->coord->lon
            );
            $citiesInsert[] = $newCity;
            if(count($citiesInsert) >= 100){
                \Illuminate\Support\Facades\DB::table('cities')->insert($citiesInsert);
                $citiesInsert = array();
            }
        }
        \Illuminate\Support\Facades\DB::table('cities')->insert($citiesInsert);
    }
}
