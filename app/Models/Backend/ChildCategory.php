<?php

namespace App\Models\Backend;

use App\Models\Backend\MediaUpload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Brand\app\Models\Brand;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ChildCategory extends Model
{
    use HasFactory;

    protected $table = 'child_categories';
    protected $fillable = ['name','slug','category_id','sub_category_id','brand_id','status','image', 'description'];

    public function category(){
        return $this->belongsTo('App\Models\Backend\Category');
    }

    public function subcategory(){
        return $this->belongsTo( SubCategory::class, 'sub_category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }


    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $mediaUpload = MediaUpload::find($this->image);

        if (!$mediaUpload) {
            // return asset('path/to/default/image.png');
            return null;
        }

        $fullPath = 'uploads/media-uploader/' . $mediaUpload->path;
        return asset('storage/' . $fullPath);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => get_attachment_image_by_id($value, 'thumb')['img_url'] ?? '',
        );
    }
}
