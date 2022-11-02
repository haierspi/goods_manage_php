<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use ff\nosql\Redis;
use models\tables\SupplierModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class GoodsController extends CookieAuthController
{
    private $pageNum = 20;

    /**
     * listSkip: 在列表内隐藏显示
     * name 输入名称
     * desc 补充说明
     * isNotField 外部字段 所有处理业务都会跳过
     *
     * type text,image,enum,html
     *    viewSkip 不显示编辑VIEW
     *    text 文本
     *       列表: 显示文本
     *       编辑:
     *           viewType
     *                 html 富文本内容
     *                 hidden: 隐藏
     *                 time:   时间选择器
     *                 默认  文本编辑
     *
     *
     *     image 图片
     *         列表: 可以增加 property 属性用来限制图片
     *         编辑:
     *     enum 单选项
     *         列表: 显示具体某一选项
     *         编辑:
     *             updateType = radio 按钮 选择
     *             updateType = select 选择框选择
     *                  multiple 多选
     *             enumDataGet = ['set'=>'关联model','sets'=>'关联集合model','title'=>'选项标题','value'=>'选项值','titleAddID'=>'选择显示关联ID']
     *
     *    search  是否需要在list也进行搜索
     *    searchTextLike  需要LIKE搜索
     *
     * updateTypeId : 强制更新字段
     * handleSkip 跳出处理阶段
     *
     * listClosure  在编辑显示阶段闭包处理 类型为闭包  function(  model,fieldKey) model {}
     * viewClosure  在编辑显示阶段闭包处理 类型为闭包  function(  model,fieldKey) model {}
     * handleClosure 在编辑更新阶段闭包处理 类型为闭包  function(  model,fieldValue, fieldKey) model {}
     *        valueTypeSet 强制设置类型
     * handleCheck 开启输入检测
     * handleCheckZeroInt 输入检测允许 int 0
     * handleCheckUnique 检查唯一
     *
     * previewSkip 跳过预览
     *
     */

    private $autoFields = [];

    public function initAutoFields($act = '')
    {

        $this->autoFields = [

            'goods_id' => ['name' => '商品编码', 'search' => 1, 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
            'goods_name' => ['name' => '商品名称', 'search' => 1, 'type' => 'text'],
            'goods_price' => ['name' => '商品价格', 'type' => 'text'],
            'goods_in_price' => ['name' => '进货价格', 'type' => 'text'],
            'goods_image' => ['name' => '商品封面图', 'type' => 'image', 'property' => 'width="50"',
            'listClosure' => function ($model, $key) {
                $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
            },
        ],
            'goods_body' => ['name' => '商品简介', 'type' => 'text', 'viewType' => 'html', 'listSkip' => 1],
            'status' => ['name' => '商品状态', 'type' => 'enum', 'enum' => ['0' => '已下架', '1' => '上架中'], 'viewType' => 'radio', 'search' => 1],


            'supplier_id' => ['name' => '供贸商', 'type' => 'enum', 'listSkip' => 1, 'enumDataGet' => ['set' => 'supplier', 'sets' => 'suppliers', 'title' => 'supplier_name'],
                'viewType' => 'select',
                'handleClosure' => function ($model, $id) {
                    $release = SupplierModel::find($id);
                    $model->supplier_name = $release->supplier_name;
                },
            ],

            'supplier_name' => ['name' => '供贸商名字', 'type' => 'text',   'viewSkip' => 1],
        ];

    }

    public function actionList()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\GoodsModel';
        $itemName = '商品管理';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = $modelClass::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "增加供贸商" => [
                    'url' => '/' . $this->controllerPath . '/update',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "修改" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/update?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
                "删除" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/del?id=' . $model->$idKey;
                    },
                    'class' => 'btn-danger',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionUpdate()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\GoodsModel';
        $itemName = '商品';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? [] : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();

            //输入检查
            $error = Admin::handleCheck($id, $update);
            if ($error != null) {
                return $error;
            }

            $itemModel = Admin::handleModel($id, $update);

            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '修改' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }

    public function actionDel()
    {

        $this->initAutoFields('Notice');
        $modelClass = '\models\tables\AdminUserModel';
        $itemName = '用户登录';

        Admin::setModel($modelClass);

        $model = Admin::$model::find($this->request->vars['id']);
        $model->delete();

        return Admin::message($itemName . ' 删除成功', '/' . $this->actionPathL);
    }

}
