<?php
namespace Gini\Controller\CGI;

class Equipment extends \Gini\Controller\REST {
    /**
     * 获取
     */
    public function getDefault($id){
        $response = [
            'status'=>'success',
            'message'=>'',
            'data'=>''
        ];
        $form = $this->form('get');
        if($id){
            $equipment = a('equipment',$id);
        }else{
            $equipment = those('equipment');
            $name = isset($form['name'])?$form['name']:'';
            $refNo = isset($form['ref_no'])?$form['ref_no']:'';
            $equipment->whose('name')->contains($name)->whose('ref_no')->contains($refNo);
            $response['total'] = $equipment->totalCount();

            if (isset($form['start']) && isset($form['per'])) {
                $equipment->limit($form['start'], $form['per']);
            }
        }
        
        if ($equipment instanceof \Gini\Those) {
            $data = [];
            foreach($equipment as $item){
                $data[] = [
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'en_name'=>$item->en_name,
                    'model_no'=>$item->model_no,
                    'location'=>$item->location.($item->location1==''?'':' '.$item->location1),
                    'contacts'=>$item->contacts,
                    'tags'=>$item->tags
                ];
            }
            $response['data'] = $data;
        }else if ($equipment instanceof \Gini\ORM\Object) {
            $response['data'] = [
                'id'=>$equipment->id,
                'name'=>$equipment->name,
                'en_name'=>$equipment->en_name,
                'model_no'=>$equipment->model_no,
                'location'=>$equipment->location.($equipment->location1==''?'':' '.$equipment->location1),
                'contacts'=>$equipment->contacts,
                'tags'=>$equipment->tags
            ];
        }
        $res = \Gini\IoC::construct('\Gini\CGI\Response\JSON', $response);
        return $res;
    }
    public function postDefault(){
        //class_exists('\Gini\Those');
        $form = $this->form('post');
       // var_dump($form);die;
        $status = 'success';
        $message = '';
        if($form){
            $equipment = a('equipment');
            $saveRes = $this->saveForm($equipment,$form);
            var_dump($saveRes);die;
            $status = $saveRes['status'];
            $message = $saveRes['message'];
        }else{
            $status = 'errror';
            $message = "no parameter";
        }
        $response = [
            'status'=>$status,
            'message'=>$message
        ];
        $res = \Gini\Ioc::construct('\Gini\CGI\Response\JSON',$response);
        return $res;
    }

    public function putDefault(){
        $form = $this->form('put');
        $id = isset($form['id'])?$form['id']:'';

        if($id!=''){
            if($form){
                $equipment = a('equipment',$id);
                $saveRes = $this->saveForm($equipment,$form);
                $status = $saveRes['status'];
                $message = $saveRes['message'];
            }else{
                $status = 'errror';
                $message = "no parameter";
            }
        }else{        
            $status = 'errror';
            $message = 'id is null';
        }
        $response = [
            'status'=>$status,
            'message'=>$message
        ];
        $res = \Gini\Ioc::construct('\Gini\CGI\Response\JSON',$response);
        return $res;
    }
    public function deleteDefault($id){
        $status = 'success';
        $message = '';
        if($id){
            $equipment = a('equipment',$id);
            $bool = $equipment->delete();
            if(!$bool){
                $status = 'error';
                $message = 'delete fail';
            }
        }else{
            $status = 'error';
            $message = 'id is null';
        }

        $response = [
            'status'=>$status,
            'message'=>$message
        ];

        $res = \Gini\Ioc::construct('\Gini\CGI\Response\JSON',$response);
        return $res;
    }

    private function saveForm($equipment,$form){
        $props = get_class_vars(get_class($equipment));
        $requireArr = ['name','incharges','contacts'];
        $status = 'error';
        $message = '';
        $bool = true;
        
        foreach($requireArr as $v){
            if(!isset($form[$v]) || $form[$v] == ''){
                $message = $v." is required";
                $bool = false;
                break;
            }
        }
        //CERS共享
        if(isset($form['share']) && $form['share'] == 1 ){
            $cersRequiredArr = ['domain','referchargerule','opencalendar','assetscode','certification','classificationcode','manucertification','manucountrycode','sharelevel'];
            foreach($cersRequiredArr as $val) {
                if(!isset($form[$val]) || $form[$val] == ''){
                    $message = $val." is required";
                    $bool = false;
                    break;
                }
            }
        }
        if($bool){
            foreach ($form as $key => $value) {
                if (array_key_exists($key, $props)) {
                    $equipment->{$key} = $value;
                }
            }
            if($equipment->save()){
                $status = "success";
            }else{
                $message = "save error";
            }
        }
        $response = [
            'status'=>$status,
            'message'=>$message
        ];
       
        return $response;
    }
}