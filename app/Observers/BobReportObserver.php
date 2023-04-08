<?php

namespace App\Observers;

use App\Models\BobReport;

class BobReportObserver
{
    /**
     * Handle the BobReport "created" event.
     *
     * @param \App\Models\BobReport $bobReport
     * @return void
     */
    public function creating(BobReport $bobReport)
    {
        // Сохраняем валюту от аккаунта или от рума, если в форме руками не
        // проставлена таковая
        if (!$bobReport->currency_id) {
            $account = $bobReport->account;
            $bobReport->currency_id = $bobReport->currency
                ? $account->currency->id
                : $account->room->currency->id;
        }
    }

    public function updating(BobReport $bobReport)
    {
        Log::info('bobReport updating');
        $bobReport->countTotalAndWin();
    }
}
