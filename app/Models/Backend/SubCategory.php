<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Brand\app\Models\Brand;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub_categories';
    protected $fillable = ['name', 'slug', 'category_id','brand_id', 'status', 'image', 'description'];

    public function category(){
        return $this->belongsTo('App\Models\Backend\Category');
    }

    public function childcategories(){
        return $this->hasMany('App\Models\Backend\ChildCategory');
    }
    public function listings(){
        return $this->hasMany('App\Models\Backend\Listing');
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => get_attachment_image_by_id($value, 'thumb')['img_url'] ?? '',
        );
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }


}
