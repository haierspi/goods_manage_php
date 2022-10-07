<?php

namespace ff\view;

use ff;
use stdClass;

class SearchView
{

    public $viewFieldsOpModelClassClass = '';
    public $viewFields = [];
    /**
     * 检查排序字段是否不允许排序
     *
     * @return void
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2021-07-15
     */
    public function isNotAllowOrder()
    {

        $orderBy = isset(ff::$app->router->request->vars['orderBy']) ? ff::$app->router->request->vars['orderBy'] : null;

        if ($orderBy) {
            if (isset($this->viewFields[$orderBy]) && $this->viewFields[$orderBy]['enableOrder']) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }

    }

    /**
     *
     * 获取排序字段和排序方向
     *
     * @param null|int $returnIndex
     * @return void
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2021-07-14
     */
    public function getOrderBy($returnIndex = null)
    {
        $orderBy = isset(ff::$app->router->request->vars['orderBy']) ? ff::$app->router->request->vars['orderBy'] : null;
        $orderDirection = isset(ff::$app->router->request->vars['orderDirection']) ? ff::$app->router->request->vars['orderDirection'] : null;

        $data = [];
        if ($orderBy && isset($this->viewFields[$orderBy]) && $this->viewFields[$orderBy]['enableOrder']) {
            $data[0] = $orderBy;
        } else {
            $data[0] = $this->defaultOrderBy;
        }
        if ($orderDirection == 'asc') {
            $data[1] = $orderDirection;
        } else {
            $data[1] = $this->defaultOrderDirection;
        }

        if (is_null($returnIndex)) {
            return $data;
        } else {
            return $data[$returnIndex];
        }
    }

    private function haveViewFields($uid)
    {

        return (new $this->viewFieldsOpModelClass)
            ->where('type', $this->typeKey)
            ->where('uid', $uid)
            ->exists();
    }

    public function getViewFields($uid, $notSet = false)
    {

        $list = [];
        if ($this->haveViewFields($uid) || $notSet) {
            $dataList = (new $this->viewFieldsOpModelClass)
                ->where('type', $this->typeKey)
                ->where('uid', $uid)
                ->orderBy('order', 'asc')
                ->select('field', 'order')
                ->get();
            $displayViewFields = [];
            foreach ($dataList as $one) {
                $field = $one->field;

                $oneField = new stdClass;
                $oneField->title = $this->viewFields[$field]['title'];
                $oneField->field = $field;
                $oneField->order = $one->order;
                $oneField->isDisplay = true;
                $oneField->type = $this->viewFields[$field]['type'];
                $oneField->enableOrder = $this->viewFields[$field]['enableOrder'];

                if ($this->getOrderBy(0) == $field) {
                    $oneField->isOrderBy = true;
                    $oneField->orderDirection = $this->getOrderBy(1);
                } else {
                    $oneField->isOrderBy = false;
                    $oneField->orderDirection = '';
                }
                
                $oneField->contentByField = $this->viewFields[$field]['contentByField'] ? $this->viewFields[$field]['contentByField'] : $field;

                $list[] = $oneField;
                $displayViewFields[] = $field;
            }

            foreach ($this->viewFields as $field => $fieldData) {

                if (\in_array($field, $displayViewFields)) {
                    continue;
                }

                $oneField = new stdClass;
                $oneField->title = $this->viewFields[$field]['title'];
                $oneField->field = $field;
                $oneField->order = 999;
                $oneField->isDisplay = false;
                $oneField->type = $this->viewFields[$field]['type'];
                $oneField->enableOrder = $this->viewFields[$field]['enableOrder'];

                if ($this->getOrderBy(0) == $field) {
                    $oneField->isOrderBy = true;
                    $oneField->orderDirection = $this->getOrderBy(1);
                } else {
                    $oneField->isOrderBy = false;
                    $oneField->orderDirection = '';
                }

                $oneField->contentByField = $this->viewFields[$field]['contentByField'] ? $this->viewFields[$field]['contentByField'] : $field;

                $list[] = $oneField;
                $displayViewFields[] = $field;
            }

            return $list;
        } else {

            $this->setViewFields($uid, array_keys($this->viewFields));
            return $this->getViewFields($uid, true);
        }

    }

    public function setViewFields($uid, $fields)
    {
        //循环设置
        $i = 1;
        foreach ($fields as $field) {

            if (isset($this->viewFields[$field])) {

                $oneModel = (new $this->viewFieldsOpModelClass)
                    ->where('type', $this->typeKey)
                    ->where('uid', $uid)
                    ->where('field', $field)
                    ->first();

                if (!$oneModel) {
                    $oneModel = new $this->viewFieldsOpModelClass;
                }
                $oneModel->type = $this->typeKey;
                $oneModel->uid = $uid;
                $oneModel->field = $field;
                $oneModel->order = $i;
                $oneModel->save();

                $i++;
            }
        }

        //清理
        $oneModel = (new $this->viewFieldsOpModelClass)
            ->where('type', $this->typeKey)
            ->where('uid', $uid)
            ->whereNotIn('field', $fields)
            ->delete();

        return true;

    }
}
