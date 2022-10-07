<?php

namespace ff\view;

use ff;
use XiangYu2038\WithXy\condition\AndCondition;

class SearchTool
{
    private $containers = [];
    private $objectProviders = [];
    private $input = [];

    public function __construct($input = null)
    {
        $this->input = $input ? $input : $input;
    }

    //$searchModule, $varKey = ''
    public function addModule($searchModule, $varKey = '')
    {

        if (is_subclass_of($searchModule, '\ff\view\SearchToolModuleBase')) {

            $varKey = $varKey ? $varKey : $searchModule->varKey;
            $searchModule->varKey = $varKey;

            $this->containers[$varKey] = [
                'varKey' => $varKey,
                'name' => $searchModule->name,
                'type' => $searchModule->type,
                'value' => $searchModule->value(),
                'hidden' => (bool) $searchModule->hidden,
                'description' => $searchModule->description,
                'dataType' => $searchModule->dataType,
                'dataSet' => $searchModule->dataSet(),
                'dataDefaultSet' => $searchModule->dataDefaultSet,
                'dataAPICarrierVarName' => $searchModule->dataAPICarrierVarName,
                'dataAPIPageLoad' => $searchModule->dataAPIPageLoad,
                'dataAPISearchVarName' => $searchModule->dataAPISearchVarName,
            ];

            $this->objectProviders[$varKey] = $searchModule;

        } else {
            throw new \Exception("Search Module  Mast Extends ff\\view\\SearchToolModuleBase", 1);
        }

    }

    public function addModules(...$parameters)
    {
        $searchModuleAry = [];
        foreach ($parameters as $k => $parameter) {
            if (is_subclass_of($parameter, '\ff\view\SearchToolModuleBase')) {
                $one = [];
                $one['searchModule'] = $parameter;
                if (is_string($parameters[$k + 1])) {
                    $one['varKey'] = $parameters[$k + 1];
                    unset($parameters[$k + 1]);
                } else {
                    $one['varKey'] = '';
                }
                unset($parameters[$k]);
                $searchModuleAry[] = $one;
            }
        }

        foreach ($searchModuleAry as $searchData) {
            $this->addModule($searchData['searchModule'], $searchData['varKey']);
        }
    }

    //获取搜索工具的模型查询条件
    public function getModulesConditions()
    {
        $searchModelCondition = new AndCondition;
        foreach ($this->objectProviders as $varKey => $objectProvider) {
            $modelCondition = $objectProvider->getModelCondition();
            if (!is_null($modelCondition)) {
                $searchModelCondition->addCondition($modelCondition);
            }
        }
        return $searchModelCondition;
    }

    public function build()
    {

        $list = [];
        foreach ($this->containers as $id => $container) {
            //闭包动态赋值
            if (\is_callable($container['dataSet'])) {
                $container['dataSet'] = $container['dataSet']();
            } else {
                $container['dataSet'] = $container['dataSet'];
            }

            $value = isset(ff::$app->router->request->vars[$id]) ? ff::$app->router->request->vars[$id] : null;
            if (\is_callable($container['value'])) {
                $container['value'] = $container['value']($value);
            } else {
                $container['value'] = $value;
            }
            $container['value'] = $container['value'];
            $list[] = $container;
        }
        return $list;
    }

}
