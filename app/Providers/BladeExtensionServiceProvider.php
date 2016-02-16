<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeExtensionServiceProvider extends ServiceProvider
{

    public function boot ()
    {
        /*Blade::extend(function ($view, $compiler) {
            $pattern = '!\@setvar\(\s*([^,]+)\s*,\s*(.+)\s*\)!';

            return preg_replace($pattern, '<?php ${$1} = $2 ?>', $view);
        });*/
        Blade::extend(function($view)
        {
            return preg_replace('/\@setvar\((.+)\)/', '<?php ${1}; ?>', $view);
        });
    }


    public function register ()
    {

    }
}
