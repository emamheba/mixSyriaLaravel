<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Brand\app\Models\Brand;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['name','slug','icon','image','status','mobile_icon', 'description','category_type', // <-- أضف هذا السطر
];



    public function brands()
    {
        return $this->hasMany(Brand::class,'category_id','id');
    }

    public function subcategories(){
        return $this->hasMany(SubCategory::class,'category_id','id');
    }

    public function listings(){
        return $this->hasMany(Listing::class,'category_id','id')->where('status',1);
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    // public function image()
    // {
    //     return env('APP_URL').'/'.$this->image;
    // }
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => get_attachment_image_by_id($value, 'thumb')['img_url'] ?? '',
        );
    }
}
