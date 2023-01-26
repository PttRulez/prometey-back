<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Proxy;
use App\Models\ProxyHistory;
use Illuminate\Database\Seeder;

class ProxyHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $proxies = Proxy::all();
        foreach ($proxies as $proxy) {
            if ($proxy->history) {
                foreach ($proxy->history as $accountId) {
                    if (Account::find($accountId)) {
                        $proxy->historyAccounts()->attach($accountId);
                    }
                }
            }
        }
    }
}
