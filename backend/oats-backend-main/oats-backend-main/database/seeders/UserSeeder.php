<?php

namespace Database\Seeders;

use League\Csv\Reader;
use Illuminate\Database\Seeder;
use App\Modules\Account\User\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileLocation = storage_path('app/data/user-accounts.csv');
        $csv = Reader::createFromPath($fileLocation, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            User::firstOrCreate([
                'id'=>$record['id'],
                'username'=>$record['username'],
                'name'=>$record['name'],
                'email'=>$record['email'],
                'password'=>$record['password'],
                'caroupoint'=>$record['caroupoint'],
                'remember_token'=>$record['remember_token']
            ]);
        }
    }
}
