<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start' => $this->start_time,
            'end' => $this->end_time,
            'start_formatted' => $this->start_time->format('Y-m-d H:i:s'),
            'end_formatted' => $this->end_time->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'status' => $this->status,
            'user' => [
                 'id' => $this->user_id,
                 'name' => $this->user->name
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
