<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use models\tables\AdminUserModel;
use models\tables\ArticleCategoryModel;
use models\tables\ArticleModel;
use models\tables\BlockchainModel;
use models\tables\GoodsModel;
use models\tables\MemberModel;
use models\tables\OrderModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class GoodsSetController extends CookieAuthController
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

        if ($act == 'brand') {
            $this->autoFields = [
                'brand_id' => ['name' => '品牌ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'brand_name' => ['name' => '品牌方名称', 'type' => 'text', 'search' => 1, 'searchTextLike' => 1],
                'brand_image' => ['name' => '品牌方图片', 'type' => 'image', 'property' => 'width="50"'],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'release') {
            $this->autoFields = [
                'release_id' => ['name' => '发行方ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'release_name' => ['name' => '发行方名称', 'type' => 'text', 'search' => 1, 'searchTextLike' => 1],
                'release_image' => ['name' => '发行方图片', 'type' => 'image', 'property' => 'width="50"'],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'copyright') {
            $this->autoFields = [
                'copyright_id' => ['name' => '版权方ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'copyright_name' => ['name' => '版权方名称', 'type' => 'text', 'search' => 1, 'searchTextLike' => 1],
                'copyright_image' => ['name' => '版权方图片', 'type' => 'image', 'property' => 'width="50"'],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'goodsCategory') {
            $this->autoFields = [
                'category_id' => ['name' => '分类ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'category_name' => ['name' => '分类名称', 'type' => 'text'],
                'category_name_en' => ['name' => '英文分类名', 'type' => 'text'],
                'category_url' => ['name' => 'URL Tag', 'type' => 'text'],
                'is_hide' => ['name' => '是否隐藏显示', 'type' => 'enum', 'enum' => ['0' => '否', '1' => '是'], 'viewType' => 'radio'],

                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'blockchain') {
            $this->autoFields = [
                'blockchain_id' => ['name' => '区块链ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'blockchain_name' => ['name' => '区块链名字', 'type' => 'text'],
                'blockchain_key' => ['name' => '区块链key', 'type' => 'text'],
                'brand_image' => ['name' => '区块链ICON', 'type' => 'image', 'property' => 'width="50"'],

                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'contractMetadata') {
            $this->autoFields = [
                'contract_metadata_id' => ['name' => 'MetaData ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id', 'search' => 1],
                'contract_metadata_name' => ['name' => 'metadata名称', 'type' => 'text', 'search' => 1, 'searchTextLike' => 1],
                'contract_metadata_description' => ['name' => 'metadata描述', 'type' => 'text'],
                'contract_metadata_image' => ['name' => 'metadata图片', 'type' => 'image', 'property' => 'width="50"', 'listSkip' => 1],
                'contract_metadata_animation_url' => ['name' => 'metadata播放媒体地址', 'type' => 'text'],
                'goods_id' => ['name' => '关联商品ID', 'type' => 'text', 'valueTypeSet' => 'int', 'viewSkip' => 1],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'contractType') {
            $this->autoFields = [
                'contract_type_id' => ['name' => '合约类型ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'contract_type_name' => ['name' => '合约类型名称', 'type' => 'text'],
                'contract_type_key' => ['name' => '合约类型Key(唯一)', 'type' => 'text'],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1],
            ];

        } else if ($act == 'contractTemplate') {
            $this->autoFields = [
                'contract_template_id' => ['name' => '合约模板ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],

                'title' => ['name' => '模板名字', 'type' => 'text'],

                'blockchain_id' => ['name' => '区块链类型', 'type' => 'enum', 'enumDataGet' => ['set' => 'blockchain', 'sets' => 'blockchains', 'title' => 'blockchain_name'],
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $blockchain = BlockchainModel::find($id);
                        $model->blockchain_name = $blockchain->blockchain_name;
                        $model->blockchain_key = $blockchain->blockchain_key;
                        $model->blockchain_icon = $blockchain->blockchain_icon;
                    },

                ],
                'blockchain_name' => ['name' => '区块链名字', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'blockchain_key' => ['name' => '区块链key', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'blockchain_icon' => ['name' => '区块链ICON', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'blockchain_address' => ['name' => '区块链地址', 'type' => 'text'],

                'contract_type' => ['name' => '合约协议类型', 'type' => 'enum',
                    'enumDataGet' => ['set' => 'contractType', 'sets' => 'contractTypes', 'title' => 'contract_type_name'],
                    'viewType' => 'select',
                ],
                'contract_network' => ['name' => '所在的网络(树图直接网络ID)', 'type' => 'text'],
                'contract_tokenuri_url_domain' => ['name' => 'tokenuri 访问地址域名', 'type' => 'text'],
                'contract_tokenuri_url_pre' => ['name' => 'tokenuri 访问地址前缀', 'type' => 'text'],
                'contract_keystore_path' => ['name' => 'keystore地址', 'type' => 'text'],
            ];

        }

    }

    public function actionBrandList()
    {
        $this->initAutoFields('brand');
        $modelClass = '\models\tables\BrandModel';
        $itemName = '品牌方';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/BrandUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/BrandUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');
    }

    public function actionBrandUpdate()
    {

        $this->initAutoFields('brand');
        $modelClass = '\models\tables\BrandModel';
        $itemName = '品牌方';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);

            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionReleaseList()
    {
        $this->initAutoFields('release');
        $modelClass = '\models\tables\ReleaseModel';
        $itemName = '发行方';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/ReleaseUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/ReleaseUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionReleaseUpdate()
    {

        $this->initAutoFields('release');
        $modelClass = '\models\tables\ReleaseModel';
        $itemName = '发行方';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionCopyrightList()
    {
        $this->initAutoFields('copyright');
        $modelClass = '\models\tables\CopyrightModel';
        $itemName = '版权方';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/CopyrightUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/CopyrightUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionCopyrightUpdate()
    {

        $this->initAutoFields('copyright');
        $modelClass = '\models\tables\CopyrightModel';
        $itemName = '版权方';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? [] : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {
            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }



    public function actionBlockchainList()
    {
        $this->initAutoFields('blockchain');
        $modelClass = '\models\tables\BlockchainModel';
        $itemName = '区块链类型';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/BlockchainUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/BlockchainUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );
        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionBlockchainUpdate()
    {

        $this->initAutoFields('blockchain');
        $modelClass = '\models\tables\BlockchainModel';
        $itemName = '区块链类型';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionContractMetadataList()
    {
        $this->initAutoFields('contractMetadata');
        $modelClass = '\models\tables\ContractMetadataModel';
        $modelPrimaryKey = 'contract_metadata_id';
        $itemName = '合约MetaData';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/ContractMetadataUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/ContractMetadataUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionContractMetadataUpdate()
    {

        $this->initAutoFields('contractMetadata');
        $modelClass = '\models\tables\ContractMetadataModel';
        $itemName = '合约MetaData';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }

    }

    public function actionContractTypeList()
    {
        $this->initAutoFields('contractType');
        $modelClass = '\models\tables\ContractTypeModel';

        $itemName = '合约协议类型';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/ContractTypeUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/ContractTypeUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionContractTypeUpdate()
    {

        $this->initAutoFields('contractType');
        $modelClass = '\models\tables\ContractTypeModel';
        $itemName = '合约协议类型';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }

    public function actionContractTemplateList()
    {
        $this->initAutoFields('contractTemplate');
        $modelClass = '\models\tables\ContractTemplateModel';

        $itemName = '合约模板';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);
        Admin::globalViewAssign();

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "添加" => [
                    'url' => '/' . $this->controllerPath . '/ContractTemplateUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/ContractTemplateUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
                "预览" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/ContractTemplatePreview?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],

            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionContractTemplateUpdate()
    {

        $this->initAutoFields('contractTemplate');
        $modelClass = '\models\tables\ContractTemplateModel';
        $itemName = '合约模板';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        if ($_POST) {

            Admin::globalViewAssign();
            $itemModel = Admin::handleModel($id, $update);

            
            Admin::handleRelationModel($itemModel, '\models\tables\GoodsModel',
                [
                    'blockchain_id',
                    'blockchain_name',
                    'blockchain_key',
                    'blockchain_icon',
                    'blockchain_address',
                    'contract_type',
                    'contract_network',
                    'contract_tokenuri_url_domain',
                    'contract_tokenuri_url_pre',
                    'contract_keystore_path',
                ],
                'contract_template_id'
            );
            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }

    public function actionContractTemplatePreview()
    {

        $this->initAutoFields('contractTemplate');
        $modelClass = '\models\tables\ContractTemplateModel';
        $itemName = '合约模板';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? 1 : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoFields);

        $idKey = Admin::$modelIdKey;

        Admin::getModel($id);
        Admin::globalViewAssign();

        viewAssign('pageTitle', $itemName . '查看');
        viewAssign('date', date('Y-m-d'));
        return viewfile('cms/common/preview');

    }

}
