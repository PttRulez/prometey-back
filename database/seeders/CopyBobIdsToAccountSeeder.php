<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class CopyBobIdsToAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = Account::all();

        $account->each(function ($item, $key) {

            if ($item->bob_id_id) {
                $bobId = $item->bobId;
                if ($bobId) {
                    $item->bob_id = $bobId->bob_id;
                } else {
                    $item->bob_id = '';
                }

            } else {
                $item->bob_id = "";
            }
            $item->save();
        });
    }
}
