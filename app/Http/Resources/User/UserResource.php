<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;



class UserResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'full_name' => $this->first_name . ' ' . $this->last_name,
      'username' => $this->username,
      'email' => $this->email,
      'phone' => $this->phone,
      'image' => $this->image,
      'about' => $this->about,
      'state_id' => $this->state_id,
      'city_id' => $this->city_id,
      'address' => $this->address,
      'email_verified' => $this->email_verified,
      'verified_status' => $this->verified_status,
      'is_suspend' => $this->is_suspend,
    ];
  }
}
