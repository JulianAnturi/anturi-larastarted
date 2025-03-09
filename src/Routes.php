<?php

namespace JulianAnturi\larastarted;

use Illuminate\Support\Facades\Route as LaravelRoute;

class Routes extends LaravelRoute
{
    public static function apiResourceWithLogs($name, $controller, $select = false, $subselect = false)
    {
        self::apiResource($name, $controller);
        if($select == true)
        self::get($name.'-select', [ $controller::class, 'select' ]);

        if($subselect == true)
        self::get($name.'-subselect', [ $controller::class, 'subselect' ]);
    }
}

