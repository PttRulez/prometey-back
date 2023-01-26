<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BobReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);

        $array = array_merge($array, [
            'nickname' => $this->account->nickname,
            'brain' => $this->account->brain,
            'bobId' => $this->account->bobId->bob_id ?? 0,
            'network_id' => $this->account->room->network_id,
            'monthName' => $this->monthName(),
        ]);

        return $array;
    }
}
