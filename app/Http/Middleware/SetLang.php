<?php

namespace App\Http\Middleware;

use App\Models\Backend\Language;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $defaultLang =  Language::where('default',1)->first();
            if (session()->has('lang')) {
                $current_lang = Language::where('slug',session()->get('lang'))->first();
                if (!empty($current_lang)){
                    Carbon::setLocale($current_lang->slug);
                    app()->setLocale($current_lang->slug);
                }else {
                    session()->forget('lang');
                }
            }else{
                Carbon::setLocale($defaultLang->slug);
                app()->setLocale($defaultLang->slug);
            }
        }catch (\Exception $exception){

        }
        return $next($request);
    }
}
