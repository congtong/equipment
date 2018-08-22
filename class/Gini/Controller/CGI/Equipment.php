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
        $equipment = a('equipment', $id);
        if (!$equipment->id) {
            $code = 404;
            $response = T('没有找到对应的仪器信息');
            goto output;
        } else {
            //格式化返回数据信息
            $response = $equipment->format();
        }
        output:
        return new Response\JSON($response, $code);
    }
    
     /**
     * 获取单个仪器信息
     * 
     * @return json 仪器详细信息
     */
    public function fetch() { 
        $equipments = those('equipment');
        foreach (['name', 'ref_no', 'location', 'tags', 'location2', 'atime', 'application_code'] as $v) {
            $this->query($equipments, $v);
        }

        if (isset($form['start']) && isset($form['per'])) {
            $equipments->limit($form['start'], $form['per']);
        }
        if (isset($form['group_id']) && $form['group_id'] != '') {
            $equipments->whose('id')->isIn(
                those('equipment/group')->whose('group')->is(a('group', (int)$form['group_id']))->get('equipment_id')
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
    /**
     * 新增仪器信息
     * 
     * @return json 返回仪器的基本信息或者报错信息
     */
    public function post() {

        $form = $this->form('post');
        $validator = new Validator;

        try {            
            $except = ['ctime'];
            $validator
                ->validate('name', !!$form['name'], T('仪器名称必填'))
                ->validate('incharges', !!$form['incharges'], T('负责人必填'))
                ->validate('contacts', !!$form['contacts'], T('联系人必填'));
            if (isset($form['share']) && $form['share'] == 1 ) {
                $validator
                    ->validate('domain', !!$form['domain'], T('主要测试和研究领域必填'))
                    ->validate('refer_charge_rule', !!$form['refer_charge_rule'], T('参考收费标准必填'))
                    ->validate('open_calendar', !!$form['open_calendar'], T('开放机时安排必填'))
                    ->validate('assets_code', !!$form['assets_code'], T('固定资产分类编必填'))
                    ->validate('certification', !!$form['certification'], T('仪器认证情况必填'))
                    ->validate('classification_code', !!$form['classification_code'], T('共享分类编码必填'))
                    ->validate('manu_certification', !!$form['manu_certification'], T('生产厂商资质必填'))
                    ->validate('manu_country_code', !!$form['manu_country_code'], T('产地国别必填'))
                    ->validate('share_level', !!$form['share_level'], T('共享特色代码必填'));
            }
            $validator->done();
            
            $equipment = a('equipment');
            $props = get_class_vars(get_class($equipment));
            foreach ($form as $key => $value) {
                if (array_key_exists($key, $props) && !array_key_exists($key, $except)) {
                    $equipment->{$key} = $value;
                }
            }

            if ($equipment->save()) {
                $response = $equipment->format();
                $id = $response['id'];
                $this->saveEquipmentGroup($id, (int)$form['group_id']);
            } else {
                $code = 500;
                $response = T("保存失败");
            }
        } catch (Validator\Exception $e) {
            $code = 400;
            $response = $validator->errors();
        }

        output:
        return new Response\JSON($response, $code);
    }

    /**
     * 修改仪器的基本信息
     * 
     * @param int id 仪器表的主键id
     * @return json 返回修改仪器的基本信息或者报错信息
     */

    public function put($id = 0) {
        
        $form = $this->form('put');

        $equipment = a('equipment', $id);
        if (!$equipment->id) {
            $code = 400;
            $response = T("没有该仪器的信息");
        } else {
            $except = ['ctime'];
            try {
                $validator = new Validator;
                $validator
                    ->validate('name', !!$form['name'], T('仪器名称必填'))
                    ->validate('incharges', !!$form['incharges'], T('负责人必填'))
                    ->validate('contacts', !!$form['contacts'], T('联系人必填'));
                if (isset($form['share']) && $form['share'] == 1 ) {
                    $validator
                        ->validate('domain', !!$form['domain'], T('主要测试和研究领域必填'))
                        ->validate('refer_charge_rule', !!$form['refer_charge_rule'], T('参考收费标准必填'))
                        ->validate('open_calendar', !!$form['open_calendar'], T('开放机时安排必填'))
                        ->validate('assets_code', !!$form['assets_code'], T('固定资产分类编必填'))
                        ->validate('certification', !!$form['certification'], T('仪器认证情况必填'))
                        ->validate('classification_code', !!$form['classification_code'], T('共享分类编码必填'))
                        ->validate('manu_certification', !!$form['manu_certification'], T('生产厂商资质必填'))
                        ->validate('manu_country_code', !!$form['manu_country_code'], T('产地国别必填'))
                        ->validate('share_level', !!$form['share_level'], T('共享特色代码必填'));
                } else {
                    array_push($except, 'domain', 'refer_charge_rule', 'open_calendar', 'assets_code', 'certification', 'classification_code', 'manu_certification', 'manu_country_code', 'share_level');
                }

                $validator->done();
                
                $props = get_class_vars(get_class($equipment));
                foreach ($form as $key => $value) {
                    if (array_key_exists($key, $props) && !array_key_exists($key, $except)) {
                        $equipment->{$key} = $value;
                    }
                }
                
                if ($equipment->save() && $this->saveEquipmentGroup($id, (int)$form['group_id'])) {
                    $response = $equipment->format();
                } else {
                    $code = 500;
                    $response = T("保存失败");
                }
            } catch (Validator\Exception $e) {
                $code = 400;
                $response = $validator->errors();
            }
        }

        output:
        return new Response\JSON($response, $code);
    }
    /**
     * 删除仪器方法
     * 
     * @param int id 仪器的基本信息
     * @return json 返回true删除成功或者报错信息
     */
    public function delete($id = 0) {
        
        $equipment = a('equipment', $id);
        
        if (!$equipment->id) {
            $code = 400;
            $response = T("不存在该条信息");
            goto output;
        } else {
            $bool = $equipment->delete();
            if (!$bool || !$this->deleteEquipmentGroup($id)) {
                $code = 500;
                $response = T('删除失败');
                goto output;
            } else {
                $response = true;
            }
        }

        output:
        return new Response\JSON($response, $code);
    }
    /**
     * 删除equipment_group表中的关系
     * 
     * @param integer $equipomentId equipmentId是equipment表的主键id
     * @return bool true删除成功 false删除失败
     */
    private function deleteEquipmentGroup($equipmentId) {
        $db = \Gini\Database::db();
        
        $success = $db->query('DELETE FROM equipment_group WHERE equipment_id = :equipment_id', [], 
        ['equipment_id' => $equipmentId]);

        return !!$success;
    }
    /**
     * 保存equipment表和group表的对应关系
     * 
     * @param equipmentId 仪器的主键id
     * @param groupId 组织结构的最底层id
     * @return bool true 保存成功 false 保存失败
     */
    private function saveEquipmentGroup($equipmentId, $groupId) {
        $bool = true;
        $bool = $this->deleteEquipmentGroup($equipmentId);
        $arr = [];
        $this->getGroup($groupId, $arr);
    
        foreach ($arr as $v) {
            $equipmentGroup = a('equipment/group');
            $equipmentGroup->equipment = a('equipment', $equipmentId);
            $equipmentGroup->group = a('group', $v);
            $bool = $equipmentGroup->save();
        }
        return $bool;
    }

    /**
     * 根据最底层id获取父级所有仪器目录
     * 
     * @param childId 组织结构的最底层的id
     * @param arrIds 父级结构的保存数组
     * @return void
     */
    private function getGroup($childId, &$arrIds = []) {
        $group = a('group', $childId);
        if ($group->id) {
            $arrIds[] = $group->id;
            $this->getGroup($group->parent_id, $arrIds);
        }
    }
}
