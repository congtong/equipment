<?php

namespace Gini\ORM;

class Equipment extends Object {
    
    public $name = 'string:40'; //仪器的名字
    public $en_name = 'string,null:60'; //仪器的英文名称
    public $mode_no = 'string,null:40'; //型号
    public $specification = 'string,null'; //规格
    public $price = 'double,default:0'; //价格 两位小数
    public $manu_at = 'string,null:10'; //制造国家
    public $manu_facturer = 'string,null:40'; //生产厂家
    public $manu_date = 'datetime'; //生产日期
    public $purchased_date = 'datetime'; //购置日期
    public $atime  = 'datetime'; //入网日期
    public $group_id = 'object:group'; //组织结构的id
    public $cat_no = 'string,null:40'; //分类号
    public $ref_no = 'string,null:100'; //仪器编号
    public $location = 'string,null:100'; //放置地址1
    public $location2 = 'string,null'; //放置地址2
    public $tech_specs = 'string,null:*'; //主要规格及技术指标
    public $features = 'string,null:*'; //主要功能及特色
    public $configs = 'string,null:*'; //主要附件及配置
    public $incharges = 'string:100'; //负责人
    public $contacts = 'string:100'; //联系人
    public $phone = 'string,null:13'; //联系电话
    public $email = 'string,null:40'; //联系邮箱
    public $tags = 'string,null:40'; //仪器分类
    public $share = 'int,default:0'; //CERS共享
    public $domain = 'string:40'; //主要测试和研究领域 逗号分隔
    public $refer_charge_rule = 'string:100'; //参考收费标准
    public $open_calendar = 'string:100'; //开放机时安排
    public $assets_code = 'string:100'; //固定资产分类编码
    public $certification = 'string:100'; //仪器认证情况
    public $alias = 'string,null:100'; //仪器别名
    public $eng_name = 'string,null:100'; //英文名称
    public $classification_code = 'string:100'; //共享分类编码
    public $application_code = 'string,null:100'; //测试研究领域代码
    public $manu_certification = 'string:100'; //生产厂商资质
    public $manu_country_code = 'string:10'; //产地国别（代码）
    public $priceunit = 'string,null:10'; //外币币种
    public $priceother = 'int,default:0'; //外币原值
    public $share_level = 'string:100'; //共享特色代码 值为各个选项的唯一标识，逗号分隔 
    public $service_users = 'string,null:100'; //知名用户
    public $other_info = 'string,null:*'; //备注
    public $school_level = 'int,default:0'; //校级设备
    public $yiqikong_share = 'int,default:0'; //进驻仪器控
    public $ctime = 'datetime'; //创建时间

    protected static $db_index = [
        'unique:ref_no','mode_no',
        'name'
    ];

    public function format() {         
        $data = [
            'id'=>$this->id,
            'name'=>$this->name,
            'en_name'=>$this->en_name,
            'model_no'=>$this->model_no,
            'tagname'=>$this->tag,
            'location'=>$this->location.($this->location1=='' ? '' : ' '.$this->location1),
            'contacts'=>$this->contacts,
            'tags'=>$this->tags
        ];
        return $data;
    }

    public function save() {
        if ($this->ctime == '0000-00-00' || !isset($this->ctime)) {
            $this->ctime = date('Y-m-d H:i:s');
        }
        return parent::save();
    }
}
