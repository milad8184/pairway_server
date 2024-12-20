<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
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
            "text_de"  => $this->text_de,
            "text_en" => $this->text_en,
            "created_by_pair" => $this->created_by_pair,
            "type" => $this->type,
        ];
    }
}
