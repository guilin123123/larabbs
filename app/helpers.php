<?php
    function route_class()
    {
        return str_replace('.','-',\Illuminate\Support\Facades\Route::currentRouteName());
    }

/**
 * @param $category_id
 * @return string
 */
    function category_nav_active($category_id)
    {
        return active_class((if_route('categories.show') && if_route_param('category',$category_id)));
    }


    function make_excerpt($value, $length = 200)
    {
        $except = preg_replace('/\r\n|\r|\n+/',' ',$value);
        return str_limit($except, $length);
    }
