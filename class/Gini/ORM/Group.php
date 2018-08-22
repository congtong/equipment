<?php

namespace Gini\ORM;

class Group extends Object {

    public $name = 'string:100'; //组织结构的名字
    public $parent_id = 'int'; //父id
    public $root_id = 'int'; //根目录id

    protected static $db_index = [
        'name','parent_id','root_id'
    ];
}
