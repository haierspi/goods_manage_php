<?php

namespace ff\view;

/*

varKey
参数名 用户和后端通讯的唯一参数
name
控件显示名称
description
控件描述
type:控件类型
select 选择框
input 输入框
rangeInput 范围输入框,需要将返回 前后两个值以逗号间隔
multipleSelect 多选框
dataType
normal 标准的
remoteAPI 远端API
dataSet
当 dataType = normal 则这里为 多选选择信息[   {title:'xx1',  value:'1'},{title:'xx2',  value:'2'} ]
当 dataType = remoteAPI 则这里为 远端API 对应的数据获取接口地址 例如 v1.0/SearchTool/SupplierList 或v1.0/SearchTool/SupplierList?type=0
dataDefaultSet
没有选择情况下的选择项
{
title => '标题'
value => '值'
}
dataAPICarrierVarName
当 dataType = remoteAPI 时, 远端API接口返回数据使用的载体变量名
dataAPIPageLoad
当 dataType = remoteAPI 时, 是否只返回一部分数据,分步查询
dataAPISearchVarName
当 dataType = remoteAPI 时, 用户提供给远端API 用户检索的参数变量
value
默认选取的值 (如果是多选或者范围输入组件, 则各个值之间用逗号间隔)

关于  dataType = remoteAPI 的远端接口

返回的格式必须是
data.{载体变量名}.data 格式,其中data 为数组 数组中对象必须包含 title 和 value
例如:
下面的返回数据(data.supplierList.data):

{
"code": 1,
"msg": "Sussed!",
"data": {
"supplierList": {
"current_page": 1,
"data": [ {
"title": "灵川仙杨子",
"value": 18111,
"id": 18111,
"full_name": "莆田市城厢区灵川仙杨子百货商行",
"abbreviation": "灵川仙杨子",
"alibaba_link": "https://shop6m874m491d360.1688.com"
}, {
"title": "爱米娜",
"value": 18110,
"id": 18110,
"full_name": "保定爱米娜商贸有限公司",
"abbreviation": "爱米娜",
"alibaba_link": "https://shop1390496013503.1688.com"
}, {
"title": "守成牛仔",
"value": 18109,
"id": 18109,
"full_name": "深圳市宝安区守成牛仔服饰商行",
"abbreviation": "守成牛仔",
"alibaba_link": "https://shop673o379j984g2.1688.com"

}],
"first_page_url": "/?page=1",
"from": 1,
"last_page": 279,
"last_page_url": "/?page=279",
"next_page_url": "/?page=2",
"path": "/",
"per_page": 10,
"prev_page_url": null,
"to": 10,
"total": 2783
}
},
"request_params": []
}

 */

abstract class SearchToolModuleBase
{
    //唯一ID
    public $varKey;
    //模型检索字段
    public $modelSearchField = '';
    //名称
    public $name;
    //类型
    public $type;
    //是否隐藏
    public $hidden = false;
    //默认值
    public $value = null;
    //搜索控件描述
    public $description = '';
    //数据类型
    public $dataType = 'normal';
    //候选数据内容
    public $dataSet = null;
    public $dataDefaultSet = [];
    //返回数据承载变量
    public $dataAPICarrierVarName = null;
    //是否分页加载
    public $dataAPIPageLoad = false;
    //知否支持API信息搜索,如果支持这里为搜索变量
    public $dataAPISearchVarName = null;

    //选择框值选择
    abstract public function dataSet();
    //获取值方法
    abstract public function value();
    //model 搜索方法
    abstract public function getModelCondition();

    public function dataSetToKeyArray($keyName = null, $force = false)
    {
        if (!is_null($this->dataSet)) {
            $dataSet = $this->dataSet()();
        }

        $keyArray = [];
        foreach ($dataSet as $oneDataSet) {
            $keyArray[$oneDataSet['value']] = $oneDataSet['title'];
        }
        if (\is_null($keyName) && !$force) {
            return $keyArray;
        } else {
            return isset($keyArray[$oneDataSet['value']]) ? $keyArray[$keyName] : null;
        }

    }

}
