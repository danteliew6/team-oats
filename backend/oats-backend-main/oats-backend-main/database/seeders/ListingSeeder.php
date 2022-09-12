<?php

namespace Database\Seeders;

use League\Csv\Reader;
use Illuminate\Database\Seeder;
use App\Modules\Listing\Models\Listing;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileLocation = storage_path('app/data/listings.csv');
        $csv = Reader::createFromPath($fileLocation, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            Listing::firstOrCreate([
                'id'=>$record['id'],
                'user_id'=>$record['user_id'],
                'title'=>$record['title'],
                'description'=>$record['description'],
                'category'=>$record['category'],
                'price'=>$record['price'],
                'listed_date'=>$record['listed_date'],
                'deprioritized'=>$record['deprioritized'],
                'created_at'=>$record['created_at'],
                'updated_at'=>$record['updated_at']
            ]);
        }
    }
}
