<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name, // Spatie will automatically return translated value for display
            'name_translations' => $this->getTranslations('name'), // Full translations for editing
            'code' => $this->code,
            'symbol' => $this->symbol,
            'description' => $this->description, // Spatie will automatically return translated value for display
            'description_translations' => $this->getTranslations('description'), // Full translations for editing
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

