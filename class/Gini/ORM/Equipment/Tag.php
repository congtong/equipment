<?php

namespace Gini\ORM\Equipment;

class Tag extends \Gini\ORM\Object {

    public $equipment = 'object:equipment';
    public $tag = 'object:tag';

    protected static $db_index = [
        'equipment','tag'
    ];
}