<?php
namespace Gini\ORM;

class Equipment extends Object {
    public $name                = 'string:100'; //仪器的名字
    public $en_name             = 'string:100';//仪器的英文名臣
    public $mode_no             = 'string:100'; //型号
    public $specification       = 'string:100';//规格
    public $price               = 'double';//价格 前段定义了两位小数
    public $manu_at             = 'string:100';//制造国家
    public $manufacturer        = 'string:100';//生产厂家
    public $manu_date           = 'datetime';//生产日期
    public $purchased_date      = 'datetime';//购置日期
    public $atime               = 'datetime';//入网日期
    public $group_id            = 'string:100';//组织结构的id？？
    public $cat_no              = 'string:100';//分类号
    public $ref_no              = 'string:100';//仪器编号
    public $location            = 'string:255';//放置地址1
    public $location2           = 'string:255';//放置地址2
    public $tech_specs          = 'string:255';//主要规格及技术指标
    public $features            = 'string:255';//主要功能及特色
    public $configs             = 'string:255';//主要附件及配置
    public $incharges           = 'string:100';//负责人
    public $contacts            = 'string:100';//联系人
    public $phone               = 'string:13';//联系电话
    public $email               = 'string:40';//联系邮箱
    public $tags                = 'string:40';//仪器分类
    public $share               = 'int,default:0';//CERS共享
    public $domain              = 'string:40';//主要测试和研究领域  多选框的唯一标识用逗号分隔
    public $referchargerule     = 'string:100';//参考收费标准
    public $opencalendar        = 'string:100';//开放机时安排
    public $assetscode          = 'string:100';//固定资产分类编码
    public $certification       = 'string:100';//仪器认证情况
    public $alias               = 'string:100';//仪器别名
    public $engname             = 'string:100';//英文名称
    public $classificationcode  = 'string:100';//共享分类编码
    public $applicationcode     = 'string:100';//测试研究领域代码
    public $manucertification   = 'string:100';//生产厂商资质
    public $manucountrycode     = 'string:10';//产地国别（代码）
    public $priceunit           = 'string:10';//外币币种
    public $priceother          = 'int';//外币原值
    public $sharelevel          = 'string:100';//共享特色代码 值为各个选项的唯一标识，逗号分隔 
    public $serviceusers        = 'string:100';//知名用户
    public $OtherInfo           = 'string:255';//备注
    public $school_level        = 'int,default:0';//校级设备
    public $yiqikong_share     = 'int,default:0';//进驻仪器控
}