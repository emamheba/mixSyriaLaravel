<?php

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ListingPromotionResource extends JsonResource
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
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'transaction_id' => $this->transaction_id,
            'bank_transfer_proof_url' => $this->bank_transfer_proof_path ? Storage::url($this->bank_transfer_proof_path) : null,
            'payment_confirmed_at' => $this->payment_confirmed_at ? $this->payment_confirmed_at->toIso8601String() : null,
            'starts_at' => $this->starts_at ? $this->starts_at->toIso8601String() : null,
            'expires_at' => $this->expires_at ? $this->expires_at->toIso8601String() : null,
            'amount_paid' => (float) $this->amount_paid,
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at->toIso8601String(),
            'package' => new PromotionPackageResource($this->whenLoaded('promotionPackage')),
            'listing' => new SimpleListingResource($this->whenLoaded('listing')),
            'user_id' => $this->user_id,
        ];
    }
}
