<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => (float)$this->price,
            'stock' => (int)$this->stock,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
