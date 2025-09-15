<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MetaDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'meta_title'              => $this->meta_title,
            'meta_tags'               => $this->meta_tags,
            'meta_description'        => $this->meta_description,
            'facebook_meta_tags'      => $this->facebook_meta_tags,
            'facebook_meta_description'=> $this->facebook_meta_description,
            'facebook_meta_image'     => $this->facebook_meta_image,
            'twitter_meta_tags'       => $this->twitter_meta_tags,
            'twitter_meta_description'=> $this->twitter_meta_description,
            'twitter_meta_image'      => $this->twitter_meta_image,
        ];
    }
}
