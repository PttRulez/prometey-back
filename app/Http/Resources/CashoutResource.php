<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);

        $array = array_merge($array, [
            'status' => $this->status,
            'trClass' => $this->trClass(),
            'createdBy' => $this->createdBy(),
            'goesFromMain' => $this->goesFromMain(),
            'instant' => $this->instant(),
            'goesToMain' => $this->goesToMain(),
            'whenOrdered' => $this->whenOrdered(),
            'orderFromAff' => $this->orderFromAff(),
            'pending' => $this->pending(),
            'comesBackIfCanceled' => $this->comesBackIfCanceled()
        ]);

        return $array;
    }
}
