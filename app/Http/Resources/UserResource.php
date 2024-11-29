<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "uuid" => $this->uuid,
            "name"  => $this->name,
            "bday" => $this->bday,
            "gender" => $this->gender,
            "username" => $this->username,
            "pair_id" => $this->pair_id,
            "points" => $this->points,
            "created_at" => $this->created_at,
        ];
    }
}
