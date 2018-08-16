<?php

namespace Gini\Controller\CGI;

use \Gini\CGI\Response;
use \Gini\CGI\Validator;

class Equipment extends Restful {

    /**
     * 获取单个仪器信息
     * 
     * @param integer $id 仪器的数据表主键id
     * @return json 仪器详细信息
     */
    public function get($id = 0) {
        $code = 200;
        $equipment = a('equipment', $id);
        if(!$equipment->id){
            $code = 404;
            $response = '没有找到对应的仪器信息';
            goto output;
        }else{
            $response = $equipment->format();
        }
        output:
        return new Response\JSON($response, $code);
    }
    
    public function fetch() { 
        $form = $this->form('get');

        $equipments = those('equipment');
        $name = isset($form['name']) ? $form['name'] : '';
        $refNo = isset($form['ref_no']) ? $form['ref_no'] : '';
        $equipments->whose('name')->contains($name)->whose('ref_no')->contains($refNo);

        if (isset($form['start']) && isset($form['per'])) {
            $equipments->limit($form['start'], $form['per']);
        }
        if (isset($form['group_id']) && $form['group_id'] != '') {
            $equipments->whose('id')->isIn(
                those('equipment/tag')->whose('tag')->is(a('tag',(int)$form['group_id']))->get('equipment_id')
            );
        }
        $response['total'] = $equipments->totalCount();
        if ($response['total']) {
            foreach ($equipments as $equipment) {
                $response['data'][] = $equipment->format();
            }
        }
        
        output:
        return new Response\JSON($response, $code);
    }
    public function post() {
        $form = $this->form('post');
        $validator = new Validator;

        try {
            $validator = new Validator;
            $validator->validate('name', !!$form['name'], '仪器名称必填')
                    ->validate('incharges', !!$form['incharges'], '负责人必填')
                    ->validate('contacts', !!$form['contacts'], '联系人必填');
            if(isset($form['share']) && $form['share'] == 1 ){
                $validator->validate('domain', !!$form['domain'], '仪器名称必填')
                    ->validate('refer_charge_rule', !!$form['refer_charge_rule'], '负责人必填')
                    ->validate('open_calendar', !!$form['open_calendar'], '开放时间必填')
                    ->validate('assets_code', !!$form['assets_code'], '开放时间必填')
                    ->validate('certification', !!$form['certification'], '开放时间必填')
                    ->validate('classification_code', !!$form['classification_code'], '开放时间必填')
                    ->validate('manu_certification', !!$form['manu_certification'], '开放时间必填')
                    ->validate('manu_country_code', !!$form['manu_country_code'], '开放时间必填')
                    ->validate('share_level', !!$form['share_level'], '开放时间必填');
            }
            $validator->done();
            
            $equipment = a('equipment');
            $props = get_class_vars(get_class($equipment));
            foreach ($form as $key => $value) {
                if (array_key_exists($key, $props)) {
                    $equipment->{$key} = $value;
                }
            }

            if ($equipment->save()) {
                $response = $equipment->format();
                $id = $response['id'];
                $this->saveEquipmentTag($id,(int)$form['group_id']);
            } else {
                $code = 500;
                $response = "保存失败";
            }
        } catch (Validator\Exception $e) {
            $code = 400;
            $response = $validator->errors();
        }

        output:
        return new Response\JSON($response,$code);
    }

    public function put($id = 0) {
        
        $form = $this->form('put');

        $equipment = a('equipment', $id);

        if (!$equipment->id) {
            $code = 400;
            $response = "没有该仪器的信息";
        } else {
            try {
                $validator = new Validator;
                $validator->validate('name', !!$form['name'], '仪器名称必填')
                      ->validate('incharges', !!$form['incharges'], '负责人必填')
                      ->validate('contacts', !!$form['contacts'], '联系人必填');
                if(isset($form['share']) && $form['share'] == 1 ){
                    $validator->validate('domain', !!$form['domain'], '仪器名称必填')
                      ->validate('refer_charge_rule', !!$form['refer_charge_rule'], '负责人必填')
                      ->validate('open_calendar', !!$form['open_calendar'], '开放时间必填')
                      ->validate('assets_code', !!$form['assets_code'], '开放时间必填')
                      ->validate('certification', !!$form['certification'], '开放时间必填')
                      ->validate('classification_code', !!$form['classification_code'], '开放时间必填')
                      ->validate('manu_certification', !!$form['manu_certification'], '开放时间必填')
                      ->validate('manu_country_code', !!$form['manu_country_code'], '开放时间必填')
                      ->validate('share_level', !!$form['share_level'], '开放时间必填');
                }
                $validator->done();
                
                $props = get_class_vars(get_class($equipment));
                foreach ($form as $key => $value) {
                    if (array_key_exists($key, $props)) {
                        $equipment->{$key} = $value;
                    }
                }
                if($equipment->save() && $this->saveEquipmentTag($id,(int)$form['group_id'])){
                    $response = $equipment->format();
                }else{
                    $code = 500;
                    $response = "保存失败";
                }
            } catch (Validator\Exception $e) {
                $code = 400;
                $response = $validator->errors();
            }
        }

        output:
        return new Response\JSON($response, $code);
    }
    public function delete($id = 0) {
        
        $equipment = a('equipment',$id);
        
        if (!$equipment->id) {
            $code = 400;
            $response = "不存在该条信息";
            goto output;
        } else {
            $bool = $equipment->delete();
            if (!$bool || !$this->deleteEquipmentTag($id)) {
                $code = 500;
                $response = '删除失败';
                goto output;
            } else {
                $response = true;
            }
        }

        output:
        return new Response\JSON($response,$code);
    }

    private function deleteEquipmentTag($equipmentId){
        $db = \Gini\Database::db();
        $res = $db->query("DELETE FROM equipment_tag WHERE equipment_id = $equipmentId");
        if ($res) return true;
        else return false;
    }

    private function saveEquipmentTag($equipmentId, $tagId){
        $bool = true;
        $bool = $this->deleteEquipmentTag($equipmentId);
        $arr = [];
        $this->getTag($tagId, $arr);
        
        foreach ($arr as $v) {
            $equipmentTag = a('equipment/tag');
            $equipmentTag->equipment = a('equipment', $equipmentId);
            $equipmentTag->tag = a('tag', $v);
            $bool = $equipmentTag->save();
        }
        return $bool;
    }

    private function getTag($childId, &$arrIds = []){
        $tag = a('tag',$childId);
        if ($tag->id) {
            $arrIds[] = $tag->id;
            $this->getTag($tag->parent_id,$arrIds);
        }
    }
}