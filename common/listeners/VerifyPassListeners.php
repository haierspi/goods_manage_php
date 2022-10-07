<?php

namespace common\listeners;

use common\logicalentity\GoodsManager;
use common\logicalentity\Purchase1688;
use common\logicalentity\SecondOrderLogic;
use ff;
use ff\nosql\redis;
use models\tables\event_model\GoodsLogModel;
use models\tables\event_model\GoodsModel;
use models\tables\event_model\SecondOrderGoodsModel;
use models\tables\GoodsAttrApply;
use models\tables\GoodsAttrApplyContent;
use models\v1_0\Goods;
use models\v1_0\GoodsLog;
use models\v1_0\GoodsSku;
use models\v1_0\GoodsVerity;

/**
 * 审核通过事件监听者
 * @param
 * @return mixed
 */
class VerifyPassListeners
{

    public function handle($event)
    {
/////调用审核接口
        $processInstanceId = $event->event['processInstanceId'];

        if (!$processInstanceId) {
            return false;
        }


        if ($event->event['result'] == 'agree') {
            $this->test($processInstanceId);
            $scmObj = ff::createObject('common\logicalentity\Scm');
            $scmObj->applySupplierPrice($processInstanceId);

            return true;

        } elseif ($event->event['result'] == 'refuse') {
            ///拒绝
            $p = redis::get($processInstanceId);
            $p = json_decode($p, true);

            $secondOrderLogic = new SecondOrderLogic;
            if (isset($p['source']) && isset($p['remark'])) {
                if (!empty($p['n_status']) && $p['n_status'] == 1) {
                    $secondOrderLogic->refuseSecondOrderApprove($processInstanceId); //审批失败
                } else {
                    $a = new Purchase1688();
                    $goods = GoodsModel::where('goods_id', $p['goods_id'])->select('goods_sn')->first();
                    $a->getSecondOrderByGoodsSn($goods->goods_sn, $p['source'], $p['remark'], 5, $event->event['remark']);
                }
            }

            if ($p['type'] == 'xj') {
                $user = (object) $p['user']['instance']['attributes'];
                if ($p['off_shelf_type'] == 1) {
                    $secondOrderLogic->refuseBySecondOrderGoodsId($p['second_order_goods_id'], false, $user);
                } else if ($p['off_shelf_type'] == 2) {
                    //获取全部的待处理数据
                    $secondOrderGoodsList = SecondOrderGoodsModel::where("goods_id", $p['goods_id'])
                        ->where("deal_status", 2)
                        ->where("second_order_status", 8)
                        ->where('is_del', 0)
                        ->select("second_order_goods_id", "deal_status", "second_order_status", "goods_id")
                        ->get();

                    if (empty($secondOrderGoodsList)) {
                        return false;
                    }

                    //调用单款下架接口下架
                    foreach ($secondOrderGoodsList as $secondOrderGoods) {
                        $secondOrderLogic->refuseBySecondOrderGoodsId($p['second_order_goods_id'], $secondOrderGoods, $user, "整款下架操作已拒绝_" . $p['second_order_goods_id']);
                    }
                }
            }

        }

        GoodsLogModel::where('process_instance_id', $processInstanceId)->update(['status' => 3]);
        return false;

    }

    public function realUpdate($value)
    {


        $pr = (new $value['object_class'])->getKeyName();
        $object_id = $value['object_id'];

        $content = json_decode($value->content, true);

        $save = $before = [];
        foreach ($content as $v) {

            //新增采购价格如果高改低不用改进货价。如果低改高，要改进货价
            if ($value['object_class'] == 'models\tables\event_model\GoodsModel' && $v['object_field'] == 'c_in_price' && $v['new_value'] > $v['old_value']) {

                $save['in_price'] = $v['new_value'];
            }

            $before[$v['object_field']] = $v['old_value'];

            $save[$v['object_field']] = $v['new_value'];

        }

        if ($value['object_class'] == 'models\tables\event_model\GoodsModel' && isset($save['in_price'])) {


            $goodsModel = GoodsModel::where('goods_id', $value->object_id)->select('goods_sn')->first();


            $goodsAttrApplyModel = new GoodsAttrApply();
            $goodsAttrApplyModel->goods_sn = $goodsModel->goods_sn;
            $goodsAttrApplyModel->apply_desc = '商品管理改进货价';
            $goodsAttrApplyModel->add_time = time();
            $goodsAttrApplyModel->admin_id = $value->operator_id;
            $goodsAttrApplyModel->is_check = 1;
            $goodsAttrApplyModel->is_type = 0;
            $goodsAttrApplyModel->check_desc = 0;
            $goodsAttrApplyModel->save();

            $goodsAttrApplyContentModel = new GoodsAttrApplyContent();
            $goodsAttrApplyContentModel->apply_id = $goodsAttrApplyModel->apply_id;
            $goodsAttrApplyContentModel->apply_name = '商品管理改进货价';
            $goodsAttrApplyContentModel->apply_content_1 = $before['in_price'];
            $goodsAttrApplyContentModel->apply_content_2 = $save['in_price'];
            $goodsAttrApplyContentModel->save();

        }

        $value['object_class']::where($pr, $object_id)->update($save);

        //非标需求 记录一条审核日志

        if ($value['object'] == 'goods') {
            $this->recordLog($value);

        }

    }

    public function realCreate($value)
    {

        $content = json_decode($value->content, true);
        $save = [];
        foreach ($content as $v) {
            $save[$v['object_field']] = $v['new_value'];
        }

        $value['object_class']::insert($save);

    }

    public function handles($processInstanceId)
    {
/////调用审核接口

        if (!$processInstanceId) {
            return false;
        }

        $log = GoodsLogModel::where('process_instance_id', $processInstanceId)->get();
        $p = redis::get($processInstanceId);
        $p = json_decode($p, true);

        foreach ($log as $v) {
            if ($v['type'] == 'updating') {
                $this->realUpdate($v);
            } elseif ($v['type'] == 'creating') {
                $this->realCreate($v);
            }
        }

        // $a =  GoodsVerity::where('log_id',15)->update(['status'=>1]);

        $log = GoodsLogModel::where('process_instance_id', $processInstanceId)->update(['status' => 2]);
        return true;

        // GoodsVerity::where('process_instance_id',$processInstanceId)->update(['status'=>2]);///审批不同意
    }

    public function recordLog($value)
    {
        $goods = GoodsModel::where('goods_id', $value['object_id'])->select('product_line', 'goods_sn')->first();
        $content = json_decode($value->content, true);

        $mananger = new GoodsManager();
        $content2 = $mananger->getDesc($value, $goods);

        $isSuppliers = false; //是否修改了供应商
        $isPrice = false;
        $isPrice3 = false; //小于3改价
        $isPrice3_6 = false; //3到6 改价
        $isPrice6 = false; //大于6改价
        $isAddPrice = false; //涨价

        foreach ($content as $v) {
            if ($v['object_field'] == 'c_in_price') {
                ////改价
                $price2 = $v['new_value'];
                $price1 = $v['old_value'];

                if ($price2 > $price1) {
                    $isAddPrice = true;
                }

                $isPrice = true;
                if ($price2 - $price1 <= 3) {
                    $isPrice3 = true;

                } elseif ($price2 - $price1 > 6) {
                    $isPrice6 = true; //大于6改价

                } else {
                    $isPrice3_6 = true; //3到6 改价
                }
            } elseif ($v['object_field'] == 'suppliers_id') {
                //修改供应商
                $isSuppliers = true;
            }

        }

        $save = [];
        $save['goods_sn'] = $goods->goods_sn;
        $save['add_time'] = time();

        $save['is_type'] = 1;
        $save['is_check'] = 1;
        //新增逻辑 如果是涨价则需要修改商品属性
        if ($isAddPrice) {
            $save['is_check'] = 0;
        }
        $save['admin_id'] = $value['operator_id'];

        $id = \DB::table('goods_attr_apply')->insertGetId($save);

        $save1 = [];
        $save1['apply_id'] = $id;
        if ($isPrice) {
            $save1['apply_name'] = '商品管理改价';
            if ($isAddPrice) {
                $save1['apply_name'] .= '，需要同步普元';
            }
        } else {
            $save1['apply_name'] = '商品管理改供应商';
        }

        $save1['apply_content_1'] = '';
        $save1['apply_content_2'] = $content2;
        $id = \DB::table('goods_attr_apply_content')->insertGetId($save1);

        return true;

    }

    public function test($processInstanceId)
    {
        $log = GoodsLogModel::where('process_instance_id', $processInstanceId)->where('status', 1)->get();

        foreach ($log as $v) {
            if ($v['type'] == 'updating') {
                $this->realUpdate($v);
            } elseif ($v['type'] == 'creating') {
                $this->realCreate($v);
            }
        }

        // $a =  GoodsVerity::where('log_id',15)->update(['status'=>1]);
        $secondOrderLogic = new SecondOrderLogic;
        $p = redis::get($processInstanceId);
        $p = json_decode($p, true);
        $goods = GoodsModel::where('goods_id', $p['goods_id'])->select('goods_sn')->first();
        if (isset($p['source']) && isset($p['remark'])) {
            if (!empty($p['n_status']) && $p['n_status'] == 1) {
                $secondOrderLogic->agreeSecondOrderApprove($processInstanceId); //审批通过
            } else {
                $a = new Purchase1688();
                $a->getSecondOrderByGoodsSn($goods->goods_sn, $p['source'], $p['remark'], 1, $trialRemark = '', $nickname = $p['nickname']);
            }
        }

        if ($p['type'] == 'xj') {
            $user = (object) $p['user']['instance']['attributes'];
            if ($p['off_shelf_type'] == 1) {
                $secondOrderLogic->offShelfBySecondOrderGoodsId($p['second_order_goods_id'], false, $user, $logData = "单款下架", $status = 2);
            } else if ($p['off_shelf_type'] == 2) {
                //获取全部的待处理数据
                $secondOrderGoodsList = SecondOrderGoodsModel::where("goods_id", $p['goods_id'])
                    ->where("deal_status", 2)
                    ->where("second_order_status", 8)
                    ->where('is_del', 0)
                    ->select("second_order_goods_id", "deal_status", "second_order_status", "goods_id")
                    ->get();

                if ($secondOrderGoodsList->isEmpty()) {
                    return false;
                }

                //调用单款下架接口下架
                foreach ($secondOrderGoodsList as $secondOrderGoods) {
                    $secondOrderLogic->offShelfBySecondOrderGoodsId($p['second_order_goods_id'], $secondOrderGoods, $user, "整款下架操作_" . $p['second_order_goods_id'], $status = 2);
                }
            }
        }

        //记录mq传输到普元
        rabbitMqPublish($goods->goods_sn, 'omspy_goods_sync');

        GoodsLogModel::where('process_instance_id', $processInstanceId)->update(['status' => 2]);
        return true;
    }

}
