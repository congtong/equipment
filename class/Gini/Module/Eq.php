<?php

namespace Gini\Module;
    
class Eq {
    
    static function setup() {
        date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');
        class_exists('\Gini\Those');

        setlocale(LC_MONETARY, \Gini\Config::get('system.locale') ?: 'zh_CN');
        \Gini\I18N::setup();
    }

    /**
     * 做路由的分发
     *
     * @param [\Gini\CGI\Router] $router
     * @return void
     */
    public static function cgiRoute($router) {
        $router->cleanUp();
        $router->any('',function ($router) {
            $router
                ->get('equipment', 'Equipment@fetch')
                ->get('equipment/{id}', 'Equipment@get')
                ->post('equipment', 'Equipment@post')
                ->put('equipment/{id}', 'Equipment@put')
                ->delete('equipment/{id}', 'Equipment@delete');
        }, ['classPrefix' => '\\Gini\\Controller\\CGI\\']);
    }
}

