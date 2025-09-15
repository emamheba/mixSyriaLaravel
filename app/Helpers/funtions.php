<?php

use App\Helpers\ModuleMetaData;
use App\Helpers\SanitizeInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


/* all helper function will be here */

/**
 * @param $option_name
 * @param $default
 * @return mixed|null
 */


function module_dir($moduleName)
{
    return 'core/Modules/' . $moduleName . '/';
}

function get_module_view($moduleName, $fileName)
{
    return strtolower($moduleName) . '::payment-gateway-view.' . $fileName;
}

if (! function_exists('global_asset')) {
    function global_asset($asset)
    {
        return app('globalUrl')->asset($asset);
    }
}

if (! function_exists('global_cache')) {
    function global_cache()
    {
        return app('globalCache');
    }
}

