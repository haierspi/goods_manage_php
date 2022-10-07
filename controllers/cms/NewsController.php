<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use ff\nosql\Redis;
use models\tables\AdminUserModel;
use models\tables\ArticleCategoryModel;
use models\tables\ArticleModel;
use models\tables\MemberModel;
use models\tables\OrderModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class NewsController extends CookieAuthController
{
    private $pageNum = 20;

/**
 * hide: 在列表内隐藏显示
 *
 * type text,image,enum,html
 *    text 文本
 *       列表: 显示文本
 *       编辑:
 *           updateType
 *                 html 富文本内容
 *                 hidden: 隐藏
 *                 none:    不参与更新
 *                 time:   时间选择器
 *                 默认  文本编辑
 *
 *     image 图片
 *         列表: 可以增加 property 属性用来限制图片
 *         编辑:
 *     enum 单选项
 *         列表: 显示具体某一选项
 *         编辑:
 *             updateType = radio 按钮 选择
 *             updateType = select 选择框选择
 *             enumDataGet = ['set'=>'关联model','sets'=>'关联集合model','title'=>'选项标题','value'=>'选项值']
 * desc 描述
 *
 * updateTypeId : 强制更新字段
 *
 * viewClosure  在编辑显示阶段闭包处理 类型为闭包  function(  model,fieldKey) model {}
 * handleClosure 在编辑更新阶段闭包处理 类型为闭包  function(  model,fieldValue, fieldKey) model {}
 *
 */

    private $autoFields = [];

    public function initAutoFields($act)
    {

        if ($act == 'Notice') {
            $this->autoFields = [
                'id' => ['name' => 'ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'title' => ['name' => '标题', 'type' => 'text'],
                'short_title' => ['name' => '短标题', 'type' => 'text'],
                'title_pic' => ['name' => '标题图片', 'type' => 'image', 'property' => 'width="50"'],

                'type' => ['name' => ' 类型 ', 'type' => 'enum', 'enum' => ['0' => '网址链接', '1' => 'NFT商品', '2' => '公告'], 'viewType' => 'radio'],
                'type_data' => ['name' => '类型值', 'type' => 'text', "desc" => "类型关联数据(链接 或者 ID), 当类型为公告时 此项不需要填写"],

                'weight' => ['name' => '排序', 'valueTypeSet' => 'int', 'type' => 'text', "desc" => "越大越靠前"],

                'content' => ['name' => '内容', 'type' => 'text', 'viewType' => 'html', 'listSkip' =>1],

                'start_at' => ['name' => '开始时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'start_time');
                    },
                ],
                'end_at' => ['name' => '结束时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'end_time');
                    },
                ],

                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' =>1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' =>1],
            ];

        } else if ($act == 'Banner') {
            $this->autoFields = [
                'id' => ['name' => 'ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'title' => ['name' => '标题', 'type' => 'text'],
                'short_title' => ['name' => '短标题', 'type' => 'text'],

                'title_pic' => ['name' => '标题图片', 'type' => 'image', 'property' => 'width="50"'],

                'type' => ['name' => ' 类型 ', 'type' => 'enum', 'enum' => ['0' => '网址链接'], 'viewType' => 'radio'],
                'type_data' => ['name' => '类型值', 'type' => 'text', "desc" => "类型关联数据(链接 或者 ID)"],

                'weight' => ['name' => '排序', 'valueTypeSet' => 'int', 'type' => 'text', "desc" => "越大越靠前"],

                'start_at' => ['name' => '开始时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'start_time');
                    },
                ],
                'end_at' => ['name' => '结束时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'end_time');
                    },
                ],

                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' =>1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' =>1],
            ];

        }
    }

    public function actionNoticeList()
    {
        $this->initAutoFields('Notice');
        $modelClass = '\models\tables\NoticeModel';
        $itemName = '公告';

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
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/NoticeUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/NoticeUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
                "删除" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/NoticeDel?id=' . $model->$idKey;
                    },
                    'class' => 'btn-danger',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');


    }

    public function actionNoticeUpdate()
    {

        $this->initAutoFields('Notice');
        $modelClass = '\models\tables\NoticeModel';

        $itemName = '公告';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);

            Redis::dels("MALL_NOTICE_INFO_*");
            Redis::del("MALL_NOTICE");

            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionNoticeDel()
    {

        $this->initAutoFields('Notice');
        $modelClass = '\models\tables\NoticeModel';
        $itemName = '公告';
        $listURL = '/News/NoticeList';

        Admin::setModel($modelClass);

        $model = Admin::$model::find($this->request->vars['id']);
        $model->delete();

        Redis::dels("MALL_NOTICE_INFO_*");
        Redis::del("MALL_NOTICE");

        return Admin::message($itemName . ' 删除成功', $listURL);
    }

    public function actionBannerList()
    {
        $this->initAutoFields('Banner');
        $modelClass = '\models\tables\BannerModel';
        $modelPrimaryKey = 'id';
        $itemName = '横幅';

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
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/BannerUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/BannerUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
                "删除" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/BannerDel?id=' . $model->$idKey;
                    },
                    'class' => 'btn-danger',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');
    }

    public function actionBannerUpdate()
    {

        $this->initAutoFields('Banner');
        $modelClass = '\models\tables\BannerModel';
        $itemName = '横幅';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            Redis::del("MALL_BANNER");

            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionBannerDel()
    {

        $this->initAutoFields('Banner');
        $modelClass = '\models\tables\BannerModel';
        $itemName = '横幅';
        $listURL = '/News/BannerList';

        Admin::setModel($modelClass);

        $model = Admin::$model::find($this->request->vars['id']);
        $model->delete();

        Redis::del("MALL_BANNER");

        return Admin::message($itemName . ' 删除成功', $listURL);
    }
}
