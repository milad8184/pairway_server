<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DateideaResource extends JsonResource
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
            "title_de"  => $this->title_de,
            "title_en" => $this->title_en,
            "description_de"  => $this->description_de,
            "description_en" => $this->description_en,
            "type" => $this->type,
        ];
    }
}
