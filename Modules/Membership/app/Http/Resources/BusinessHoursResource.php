<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessHoursResource extends JsonResource
{
    public function toArray($request)
    {
        $dayOfWeek = json_decode($this->day_of_week, true);
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'business_hours' => [
                'days' => $dayOfWeek['days'] ?? [],
                'opening_times' => $dayOfWeek['opening_times'] ?? [],
                'closing_times' => $dayOfWeek['closing_times'] ?? [],
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
