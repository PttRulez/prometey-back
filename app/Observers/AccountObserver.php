<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\BobReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     *
     * @param \App\Models\Account $account
     * @return void
     */
    public function saving(Account $account)
    {
        if ($account->bob_id_id == 0) {
            $account->bob_id_id = NULL;
        }
        if (!$account->currency_id) {
            $account->currency_id = $account->room->currency->id;
        }
        foreach (['limits_group', 'limits', 'disciplines'] as $prop) {
            if (in_array($account[$prop], [[], [''], [null], ''])) {
                $account[$prop] = null;
            } elseif (is_array($account[$prop]) && !empty($account[$prop])) {
                $a = $account[$prop];
                sort($a);
                $account[$prop] = $a;

                $account[$prop] = array_values(array_diff($account[$prop], [null]));
            }
        }

        if((!$account->isActiveOrStopped()) && $account->bobId()->exists()) {
            $account->bobId->fill(['profile_id' => null])->save();
        }

    }

    public function saved(Account $account)
    {
        if ($account->proxy_id) {
            $account->proxy->historyAccounts()->syncWithoutDetaching($account->id);
        }

        if ($account->isActive()) {
            $account->createThisMonthReport();
        }
    }

    public function created(Account $account)
    {
         // Create bob-report for this month if doesn't exist
        $now = Carbon::now();

        $parameters = ['year' => $now->year, 'month' => $now->month,'account_id' => $account->id];

        BobReport::firstOrCreate($parameters, $parameters);
    }

    /**
     * Handle the Account "retrieved" event.
     *
     * @param \App\Models\Account $account
     * @return void
     */
    public function retrieved(Account $account)
    {
        foreach (['limits_group', 'limits', 'disciplines'] as $prop) {
            if (in_array($account[$prop], [[], [''], [null], ''])) {
                $account[$prop] = null;
            }
        }
    }
}
