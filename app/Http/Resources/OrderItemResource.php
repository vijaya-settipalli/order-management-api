<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ],
            'quantity' => (int)$this->quantity,
            'price' => (float)$this->price,
            'line_total' => (float) bcmul((string)$this->price, (string)$this->quantity, 2),
        ];
    }
}
