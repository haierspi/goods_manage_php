<?php
namespace controllers\cms;

use common\Admin;
use common\ErrorCode;
use common\SussedCode;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use models\tables\AdminUserModel;
use models\tables\ArticleCategoryModel;
use models\tables\ArticleModel;
use models\tables\MemberModel;
use models\tables\OrderModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class OrderController extends CookieAuthController
{
    private $pageNum = 20;

    private $autoFields = [];

    public function initAutoFields($act = '')
    {

        if ($act == '') {

            $this->autoFields = [

                'order_id' => [
                    'name' => '订单ID',
                    'type' => 'text',
                    'viewType' => 'hidden',
                    'updateTypeId' => 'id',
                    'search' => 1,

                ],
                'order_sn' => [
                    'name' => '订单号',
                    'type' => 'text',
                    'search' => 1,
                ],

                'uid' => [
                    'name' => '用户uid',
                    'type' => 'text',
                    'search' => 1,

                    'listClosure' => function ($model, $key) {
                        if ($model->$key) {
                            $model->$key = '<a href="/Member/List?uid=' . $model->member->uid . '" target="_blank">' . $model->member->nickname . '</a>';
                        } else {
                            $model->$key = '';
                        }
                    },
                ],

                'goods_id' => [
                    'name' => '下单商品',
                    'type' => 'text',
                    'search' => 1,

                    'listClosure' => function ($model, $key) {

                        $title = $model->goods_name . ' (' . $model->goods_price . ' 元 / 件)';

                        if ($model->$key) {
                            $model->$key = '<a href="/Goods/List?goods_id=' . $model->goods->goods_id . '" target="_blank">' . $title . '</a>';
                        } else {
                            $model->$key = $title;
                        }
                    },
                ],

                'goods_type' => [
                    'name' => '商品类型',
                    'type' => 'enum',
                    'enum' => [
                        '0' => '实物&NFT',
                        '1' => 'NFT',
                        '2' => '实物礼包',
                        '3' => 'NFT盲盒',
                    ],
                    'viewType' => 'radio',
                    'search' => 1,
                ],

                'goods_total_cost' => [
                    'name' => '订单总价',
                    'type' => 'text',
                ],

                'goods_buy_num' => [
                    'name' => '购买数量',
                    'type' => 'text',
                ],

                'payment' => [
                    'name' => '是否支付',
                    'type' => 'enum',
                    'enum' => [
                        '0' => '否',
                        '1' => '是',
                    ],
                    'viewType' => 'radio',
                    'search' => 1,
                ],

                'payment_type' => [
                    'name' => '支付通道',
                    'type' => 'text',
                ],

                'payment_pay_transaction_id' => [
                    'name' => '支付交易号',
                    'type' => 'text',
                ],

                // 'payment_datetime' => [
                //     'name' => '支付时间',
                //     'type' => 'text',
                // ],



                'is_shiped' => [
                    'name' => '是否铸造',
                    'type' => 'enum',
                    'enum' => [
                        '0' => '否',
                        '1' => '是',
                    ],
                    'viewType' => 'radio',
                    'search' => 1,

                ],

                'status' => [
                    'name' => '订单状态',
                    'type' => 'enum',
                    'enum' => [
                        '0' => '已关闭',
                        '1' => '未支付',
                        '2' => '已支付',
                        '3' => '已发货',
                    ],
                    'viewType' => 'radio',

                    'search' => 1,

                ],

                'express_company' => [
                    'name' => '快递公司',
                    'type' => 'text',
                    'handleCheck' => 1,
                ],

                'express_number' => [
                    'name' => '快递单号',
                    'type' => 'text',
                    'handleCheck' => 1,

                ],

                'hannels_id' => [
                    'name' => '渠道来源',
                    'type' => 'enum',
                    // 'enum' => [
                    //     '0' => '已关闭',
                    //     '1' => '未支付',
                    //     '2' => '已支付',
                    //     '3' => '超卖需退款',
                    // ],
                    'enumDataGet' => ['sets' => 'hannels', 'title' => 'hannels_title', 'titleAddID' => true],
                    'viewType' => 'radio',
                    'search' => 1,
                    'listSkip' => 1,
                ],

                'created_at' => [
                    'name' => '订单创建时间',
                    'type' => 'text',
                    'viewSkip' => 1,
                ],

            ];
        } else if ($act == 'express') {
            $this->autoFields = [

                'order_id' => [
                    'name' => '订单ID',
                    'type' => 'text',
                    'viewType' => 'hidden',
                    'updateTypeId' => 'id',
                    'search' => 1,

                ],

                'order_sn' => [
                    'name' => '订单号',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'goods_name' => [
                    'name' => '下单商品',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'goods_total_cost' => [
                    'name' => '订单总价',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'goods_buy_num' => [
                    'name' => '购买数量',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'link_man' => [
                    'name' => '收货人',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'link_phone' => [
                    'name' => '收货电话',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'link_address' => [
                    'name' => '收货地址',
                    'type' => 'text',
                    'disabled' => 1,
                ],

                'express_company' => [
                    'name' => '快递公司',
                    'type' => 'text',
                    'handleCheck' => 1,
                ],

                'express_number' => [
                    'name' => '快递单号',
                    'type' => 'text',
                    'handleCheck' => 1,

                ],

            ];
        }

    }

    public function actionList()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\OrderModel';
        $itemName = '订单列表';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        $this->loadCategory();

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        Admin::globalViewAssign();

        $idKey = (new $modelClass)->getKeyName();

        $list = $modelClass::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //操作按钮
        Admin::listOperateButtons(
            [
                "发货" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/Express?id=' . $model->$idKey;
                    },
                    'js' => 'DialogView',
                    'class' => 'btn-primary',
                    'display' => function ($model) use ($idKey) {
                        if ($model->status == 2 && in_array($model->goods_type,[0,2])) {
                            return true;
                        }
                        return false;
                    },
                ],

                "修改发货" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/Express?id=' . $model->$idKey;
                    },
                    'js' => 'DialogView',
                    'class' => 'btn-primary',
                    'display' => function ($model) use ($idKey) {
                        if ($model->status == 3) {
                            return true;
                        }
                        return false;
                    },
                ],

            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionExpress()
    {

        $this->initAutoFields('express');

        $modelClass = '\models\tables\OrderModel';
        $itemName = '订单发货';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? [] : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

  

        if ($_POST) {

            Admin::globalViewAssign();

            //输入检查
            $error = Admin::handleCheck($id, $update,true);
            if ($error != null) {

                return ErrorCode::ERROR(
                    [
                        'msg' => $error,
                    ]
                );

            }

           $itemModel = Admin::handleModel($id, $update);
           $itemModel->status = 3;
           $itemModel->save();

            return SussedCode::SUSSED(
                [
                    'msg' => '发货成功'
                ]
            );

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            $content = viewfile('cms/common/dialog');

            return SussedCode::SUSSED(
                [
                    'data' => [
                        'title' => $itemName,
                        'content' => $content,
                    ],
                ]
            );

        }

    }

}
