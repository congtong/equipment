<?php

namespace Gini\ORM\Equipment;

class Group extends \Gini\ORM\Object {

    public $equipment = 'object:equipment';
    public $group = 'object:group';

    protected static $db_index = [
        'equipment', 'group'
    ];
}
