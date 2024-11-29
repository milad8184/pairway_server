<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PairResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user1_id"  => $this->user1_id,
            "user2_id" => $this->user2_id,
            "created_at" => $this->created_at,
            "anniversary_date" => $this->anniversary_date,
            "connectkey" => $this->connectkey,
        ];
    }
}
