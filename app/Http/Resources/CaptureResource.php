<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaptureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identify' => $this->id,
            'idEquipment' => $this->idEquipment,
            'plate' => $this->plate,
            'dt_created' => Carbon::make($this->created_at)->format('Y-m-d'),
            'status' => $this->statusSend
        ];
    }
}
