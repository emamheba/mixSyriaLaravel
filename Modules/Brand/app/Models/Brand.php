<?php

namespace Modules\Brand\app\Models;

use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\SubCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Brand\Database\factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'brands';
    protected $fillable = ['title', 'url', 'image', 'category_id','stauts'];

    protected static function newFactory(): BrandFactory
    {
        return BrandFactory::new();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'brand_id');
    }

    public function childcategories(){
        return $this->hasMany(ChildCategory::class,'brand_id','id');
    }
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => get_attachment_image_by_id($value, 'thumb')['img_url'] ?? '',
        );
    }


}
