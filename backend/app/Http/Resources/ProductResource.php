<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => number_format((float)$this->price, 2, '.', ''),
            'price_formatted' => '$' . number_format((float)$this->price, 2),
            'stock' => $this->stock,
            'stock_status' => $this->getStockStatus(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Get stock status based on quantity.
     *
     * @return string
     */
    private function getStockStatus(): string
    {
        if ($this->stock === 0) {
            return 'out_of_stock';
        }

        if ($this->stock < 10) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'Product');
    }
}
