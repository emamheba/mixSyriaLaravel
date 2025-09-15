<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use App\Models\Backend\Page;
use App\Models\Backend\StaticOption;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Blog\app\Models\Blog;

class FrontendController extends Controller
{
    public function home_page()
    {
        try {
            $home_page_id = get_static_option('home_page');
            $page_details = Page::find($home_page_id);

        }catch (\Exception $exception){
            $page_details = null;
        }

        return view('frontend.pages.frontend-home',compact('page_details'));

    }

    public function dynamic_single_page($slug)
    {
        try {
            $page_post = Page::where('slug', $slug)->first();
            $user_details = User::where(['username' => $slug])->first();

            $preserved_pages = [
                'home_page',
                'service_list_page',
                'blog_page',
            ];

            $static_option = StaticOption::whereIn('option_name', $preserved_pages)->get()->mapWithKeys(function ($item) {
                return [$item->option_name => $item->option_value];
            })->toArray();

            $pages_id_slugs = Page::whereIn('id', array_values($static_option))->get()->mapWithKeys(function ($item) {
                return [$item->id => $item->slug];
            })->toArray();

            if (in_array($slug, $pages_id_slugs) && $slug === $pages_id_slugs[$static_option['home_page']]) {
                return redirect()->route('homepage');
            } elseif(!is_null($user_details)){
                return $this->_user_profile($user_details);
            }

            $page_type = 'page';
            if (!is_null($page_post)) {
                return view('frontend.pages.dynamic.dynamic-single', compact(['page_post','page_type']));
            }

        }catch (\Exception $exception){

        }

        abort(404);
    }
}
