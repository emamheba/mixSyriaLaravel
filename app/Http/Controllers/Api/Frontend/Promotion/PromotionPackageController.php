<?php

namespace App\Http\Controllers\Api\Frontend\Promotion;

use App\Http\Controllers\Controller;
use App\Http\Resources\Promotion\PromotionPackageResource;
use App\Models\PromotionPackage;
use Illuminate\Http\Request;

class PromotionPackageController extends Controller
{
    public function index()
    {
        $packages = PromotionPackage::where('is_active', true)->orderBy('price')->get();
        return PromotionPackageResource::collection($packages);
    }
}
