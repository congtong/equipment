<?php

namespace Gini\Module {
    
    class Eq {
        
        static function setup() {
            date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');
            //error_Log(2323);
            class_exists('\Gini\Those');

            setlocale(LC_MONETARY, \Gini\Config::get('system.locale') ?: 'zh_CN');
            \Gini\I18N::setup();
        }
    }
}
