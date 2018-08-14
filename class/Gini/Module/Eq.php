<?php
namespace Gini\Module {
    
    class Eq {
        
        static function setup() {
            date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');

            class_exists('\Gini\Those');

            setlocale(LC_MONETARY, \Gini\Config::get('system.locale') ?: 'zh_CN');
            \Gini\I18N::setup();
        }
    }
}

namespace {

    if (function_exists('m')) {
        die('m() was declared by other libraries, which may cause problems!');
    } else {
        function m($name, $params = null)
        {
            $class_name = '\Gini\Model\\'.str_replace('/', '\\', $name);

            return \Gini\IoC::construct($class_name, $params);
        }
    }

}