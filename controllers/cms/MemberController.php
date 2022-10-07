<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use models\tables\AdminUserModel;
use models\tables\MemberModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class MemberController extends CookieAuthController
{

    private $autoFields = [
        'uid' => ['name' => 'UID', 'type' => 'text', 'search' => 1],
        'nickname' => ['name' => '昵称', 'type' => 'text', 'search' => 1],
        'name' => ['name' => '名字', 'type' => 'text'],
        'avatar' => ['name' => '头像', 'type' => 'image', 'property' => 'width="50"'],
        'avatar_url' => ['name' => '微信头像', 'type' => 'image', 'property' => 'width="50"'],
        'mobile' => ['name' => '手机号码', 'type' => 'text', 'search' => 1],

        'is_validate' => ['name' => '是否身份验证', 'type' => 'enum', 'enum' => ['0' => '否', '1' => '是'], 'search' => 1, 'searchTextLike' => 0],
        'idcard' => ['name' => '身份证号', 'type' => 'text'],

        'openid' => ['name' => 'openid', 'type' => 'text', 'search' => 1],
        'unionid' => ['name' => 'unionid', 'type' => 'text', 'search' => 1],

        'gender' => ['name' => '性别', 'type' => 'text'],
        'language' => ['name' => '语言', 'type' => 'text'],
        'city' => ['name' => '城市', 'type' => 'text'],
        'province' => ['name' => '省份', 'type' => 'text'],
        'country' => ['name' => '国家', 'type' => 'text'],
        'avatarUrl' => ['name' => '微信的头像', 'type' => 'image', 'property' => 'width="50"'],
        'updated_at' => ['name' => '更新时间', 'type' => 'text'],
        'created_at' => ['name' => '创建时间', 'type' => 'text'],
        'hannels_id' => [
            'name' => '渠道来源',
            'type' => 'enum',
            // 'enum' => [
            //     '0' => '已关闭',
            //     '1' => '未支付',
            //     '2' => '已支付',
            //     '3' => '超卖需退款',
            // ],
            'enumDataGet' => [ 'sets' => 'hannels', 'title' => 'hannels_title', 'titleAddID' => true],
            'viewType' => 'radio',
            'search' => 1
        ],

    ];

    private $pageNum = 20;
    public function actionList()
    {

        $modelClass = '\models\tables\MemberModel';
        $itemName = '会员列表';

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

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

}
