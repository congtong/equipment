<?php

namespace Gini\Controller\CGI;

class Restful extends \Gini\Controller\Rest {
 
    private $operators = [
        'ne' => 'isNot',
        'in' => 'isIn',
        'ni' => 'isNotIn',
        'gt' => 'isGreaterThan',
        'ge' => 'isGreaterThanOrEqual',
        'lt' => 'isLessThan',
        'le' => 'isLessThanOrEqual',
        'bt' => 'isBetween',
    ];
    /**
     * 查询语句封装
     * 
     * @param \Gini\Those 数据对象
     * @param string field 查询字段
     * @return void
     */
    public function query(\Gini\Those $object, $field) {
        $form = $this->form('get');

        if (isset($form[$key])) {
            $value = $form[$key];
            if (is_array($value)) $this->filter($object, $field, $value);
            else $object->whose($field)->is($value);
        }
    }

    /**
     * 筛选条件方法
     * 
     * @param \Gini\Those object 数据对象
     * @param string field 查询字段
     * @param array $array 条件数组【‘操作’, '数值1'，‘数值2’】
     */ 
    public function filter(\Gini\Those $object, $filed, $array) {
        $op = array_shift($array);
        if (!isset($this->operators[$op])) return;
        $operator = $this->operators[$op];
        switch ($op) {
            case 'bt':
                $object->whose($field)->isBetween($array[0], $array[1]);
        } 
    }

}