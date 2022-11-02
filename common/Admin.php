<?php
namespace common;

use ff;
use ff\database\AdminUserAuthModel;
use ff\database\Model;
use ff\helpers\Cookie;
use models\tables\ContractTypeModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class Admin
{
    const COOKIE_PRE = 'c_';
    const TOKEN_EXPIRATION = 86400;
    const ENCRYPT_KEY = 'ADMIN_TOKEN';
    const COOKIE_NAME = 'login_status';
    const PAGINATE_SHOWLINK_SIZE = 2;

    public static $cookiepre = '';

    public static $autoFields = [];
    public static $listOperateButtons = [];
    public static $listAddButtons = [];

    public static $model = null;

    public static $modelIdKey = null;

    public static function setAutoFields($autoFields)
    {
        self::$autoFields = $autoFields;
        viewAssign('autoFields', $autoFields);
    }

    public static function message($message, $data = '', $icon = '0')
    {
        viewAssign('message', $message);
        viewAssign('data', $data);
        viewAssign('baseurl', constant('RUNTIME_HTTP_HOST'));
        return viewfile('cms/common/message');
    }

    public static function exit_message($message, $data = '', $icon = '0')
    {
        viewAssign('message', $message);
        viewAssign('data', $data);
        viewAssign('baseurl', constant('RUNTIME_HTTP_HOST'));
        echo viewfile('cms/common/message');
        exit;
    }

    public static function login($token)
    {
        Cookie::setCookie(self::COOKIE_NAME, $token, self::TOKEN_EXPIRATION, self::COOKIE_PRE);
    }

    public static function logout()
    {
        Cookie::setCookie(self::COOKIE_NAME, '', -1, self::COOKIE_PRE);
    }

    public static function link($link)
    {

        if (strpos($link, '?') !== false) {
            return $link . '&';
        } else {
            return $link . '?';
        }
    }

    public static function setModel($model)
    {
        self::$model = is_string($model) ? new $model : $model;

        self::$modelIdKey = self::$model->getKeyName();
    }

    public static function getListSearchContent($vas = [])
    {

        $vars = ff::$app->router->request->vars;

        $html = ' <form id="list_search" method="get" enctype="multipart/form-data" autocomplete="off" action="' . self::listSearchURL($vas) . '"  style="float: right;">';
        foreach (self::$autoFields as $key => $field) {

            if (!$field['search']) {
                continue;
            }

            $html .= ($html == '' ? '筛选列表: ' : '');
            if ($field['type'] == 'text') {
                $html .= '<input type="text" ' . ($vars[$key] ? ' class="search_select"' : '') . 'name="' . $key . '" maxlength="400" value="' . $vars[$key] . '" placeholder="' . $field['name'] . '" >' . "\r\n";
            } elseif ($field['type'] == 'enum') {
                if (isset($field['enumDataGet']) && !isset($field['enum'])) {

                    
                    $dataSets = $field['enumDataGet']['sets'];
                    $titleName = $field['enumDataGet']['title'];

                    $dataSets = self::$model->$dataSets();

                    foreach ($dataSets as $relationItem) {
                        $keyName = $field['enumDataGet']['value'] ? $field['enumDataGet']['value'] : $relationItem->getKeyName();

                        $field['enum'][$relationItem->$keyName] = $relationItem->$titleName;
                    }
                }
                $html .= '<select class="js-example-basic-single' . (isset($vars[$key]) && $vars[$key] !== '' ? ' search_select' : '') . '" data-placeholder=""  data-var="type" name="' . $key . '" id="">';
                $html .= '<option class="italic" value="" ' . (!isset($vars[$key]) || $vars[$key] === '' ? 'selected="selected" ' : '') . '> * ' . $field['name'] . '</option>';
                foreach ($field['enum'] as $k => $v) {
                    $html .= '<option value="' . $k . '" ' . (isset($vars[$key]) && $vars[$key] !== '' && $vars[$key] == $k ? 'selected="selected" ' : '') . '> - ' . $v . '</option>';
                }

                $html .= '</select>' . "\r\n";
            }

        }
        $html .= '</form>';
        return $html;
    }

    public static function listSearchCondition()
    {

        $vars = ff::$app->router->request->vars;

        $searchModelCondition = new AndCondition();

        foreach (self::$autoFields as $key => $field) {

            if (!$field['search']) {
                continue;
            }

            if (isset($vars[$key]) && $vars[$key] !== '') {

                if (is_callable($field['searchClosure']) && $field['searchClosure'] instanceof \Closure) {
                    $searchVal = $field['searchClosure']($vars[$key]);
                } else {
                    $searchVal = $vars[$key];
                }

                if ($field['type'] == 'text') {
                    if ($field['searchTextLike']) {
                        $c = new LikeCondition($key, $searchVal);
                    } else {
                        $c = new Condition($key, $searchVal);
                    }

                } elseif ($field['type'] == 'enum') {
                    if ($field['searchTextLike']) {
                        $c = new LikeCondition($key, $searchVal);
                    } else {
                        $c = new Condition($key, $searchVal);
                    }
                }

                $searchModelCondition->addCondition($c);
            }

        }
        return $searchModelCondition;
    }

    public static function listOperateButtons($listOperateButtons)
    {

        self::$listOperateButtons = $listOperateButtons;
    }

    public static function getListOperateButtons($item)
    {

        $html = '';
        foreach (self::$listOperateButtons as $title => $buttons) {
            if($buttons['display'] && is_callable($buttons['display']) && $buttons['display'] instanceof \Closure){
                $display = $buttons['display']($item);
            }else{
                $display = true;
            }
            if($display){
                if($buttons['js']){
                    $html .= ' <a class="btn btn-sm ' . $buttons['class'] . '" onclick="'.$buttons['js'].'(\''.$buttons['url']($item).'\',this)" href="javascript:void(0)">' . $title . '</a>';
                }else{
                    $html .= ' <a class="btn btn-sm ' . $buttons['class'] . '" href="' . $buttons['url']($item) . '">' . $title . '</a>';
                }
                
            }
            
        }
        return $html;
    }

    public static function listAddButtons($listOperateButtons)
    {

        self::$listAddButtons = $listOperateButtons;
    }

    public static function getListAddButtons()
    {

        $html = '';
        foreach (self::$listAddButtons as $title => $buttons) {
            $html .= ' <a class="btn btn-sm ' . $buttons['class'] . '" href="' . $buttons['url'] . '">' . $title . '</a>';
        }
        return $html;
    }

    public static function listSearchURL($vas2)
    {

        $vars = ff::$app->router->request->vars;
        $vars = \array_merge($vars, $vas2);
        unset($vars['page']);
        $routerPath = '/' . ff::$app->router->actionPath;

        return $routerPath;

    }

    
    public static function dialogURL()
    {

        $routerPath = '/' . ff::$app->router->actionPath;
        
        return $routerPath;

    }

    public static function listPaginateURL()
    {

        $vars = ff::$app->router->request->vars;
        unset($vars['page']);

        $paginateUrl = '';

        $routerPath = '/' . ff::$app->router->actionPath;
        if ($vars) {
            $paginateUrl = $routerPath . '?' . http_build_query($vars) . '&';
        } else {
            $paginateUrl = $routerPath . '?';
        }

        return $paginateUrl;

    }

    public static function listHeader()
    {

        $html = '';
        foreach (self::$autoFields as $key => $field) {
            if ($field['listSkip']) {
                continue;
            }

            $html .= "<th>{$field['name']}</th>";

        }
        return $html;
    }

    public static function listContent($item)
    {

        $html = '';
        foreach (self::$autoFields as $key => $field) {

            if ($field['listSkip']) {
                continue;
            }

            if (is_callable($field['listClosure']) && $field['listClosure'] instanceof \Closure) {
                $field['listClosure']($item, $key);
            }

            if ($field['type'] == 'image') {
                $html .= "<td><img src=\"" . $item->$key . "\" {$field['property']}></td>";
            } elseif ($field['type'] == 'file') {
                $html .= "<td><a href=\"" . $item->$key . "\" {$field['property']} target=\"_blank\">" . $item->$key . "</a></td>";
            } elseif ($field['type'] == 'text') {
                $html .= "<td>" . $item->$key . "</td>";
            } elseif ($field['type'] == 'enum') {

                if (isset($field['enumDataGet']) && !isset($field['enum'])) {

                    $dataSets = $field['enumDataGet']['sets'];
                    $titleName = $field['enumDataGet']['title'];
                    $titleAddID = $field['enumDataGet']['titleAddID'];

                    $dataSets = $item->$dataSets();

                    foreach ($dataSets as $relationItem) {
                        $keyName = $field['enumDataGet']['value'] ? $field['enumDataGet']['value'] : $relationItem->getKeyName();

                        $field['enum'][$relationItem->$keyName] = $relationItem->$titleName . ($titleAddID ? ' ID:' . $relationItem->$keyName . '' : '');
                    }
                }

                $html .= "<td>" . $field['enum'][$item->$key] . "</td>";
            }

        }
        return $html;
    }

    public static function viewContent($item)
    {

        $html = '';
        foreach (self::$autoFields as $key => $field) {

            if ($field['viewSkip']) {
                continue;
            }

            if (is_callable($field['viewClosure']) && $field['viewClosure'] instanceof \Closure) {
                $field['viewClosure']($item, $key);
            }

   

            $disabled = $field['disabled']?'disabled="disabled"':'';

            $name = $field['updateTypeId'] ? $field['updateTypeId'] : 'update[' . $key . ']';

            if ($field['type'] == 'image') {
                $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . '</li>
                    <li class="item-descr-msg">
                        <div  class="CloudImgUploader">
                            <input type="text" name="' . $name . '"  class="target"  '.$disabled.' value="' . $item->$key . '" style="width:360px;" />&nbsp;&nbsp;<button type="button"   id="uploadtocloud_' . $key . '" class="uploadtocloud greensmallbtn " >上传文件</button>

                            <div class="cloudbtn_progress upload_progress" ></div>
                            <div class="progressstatus img_view">';
                $html .= $item->$key ? '<img src="' . $item->$key . '" height="200">' : '';
                $html .= '</div>
                        </div>
                    </li>
                </ul>
                ';

            } elseif ($field['type'] == 'file') {
                $html .= '<ul class="tab-pane-media">
                        <li class="item-descr-title">' . $field['name'] . '</li>
                        <li class="item-descr-msg">
                            <div  class="CloudFileUploader">
                                <input type="text" name="' . $name . '"  '.$disabled.'  class="target" value="' . $item->$key . '" style="width:360px;" />&nbsp;&nbsp;<button type="button"   id="uploadtocloud_' . $key . '" class="uploadtocloud greensmallbtn " >上传文件</button>

                                <div class="cloudbtn_progress upload_progress" ></div>
                                <div class="progressstatus img_view">';
                $html .= '</div>
                            </div>
                        </li>
                    </ul>
                    ';
            } elseif ($field['type'] == 'text') {
                if ($field['viewType'] == 'hidden') {
                    $html .= '<input type="hidden"  '.$disabled.' name="' . $name . '" value="' . $item->$key . '" />';

                } elseif ($field['viewType'] == 'none') {
                    continue;

                } elseif ($field['viewType'] == 'time') {

                    $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . '</li>
                    <li class="item-descr-msg"><input type="text"  '.$disabled.' name="' . $name . '"  onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" style="width:360px;" value="' . $item->$key . '"/> ' . $field['desc'] . ' </li>
                    </ul>';

                } elseif ($field['viewType'] == 'textarea') {
                    $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . '</li>
                    <li class="item-descr-msg">
                    <textarea id="message"  '.$disabled.' name="' . $name . '" style="width:360px;">' . $item->$key . '</textarea>' . $field['desc'] . ' </li>
                    </ul>';
                } elseif ($field['viewType'] == 'html') {

                    $htmlId = 'html_edit_' . $key;
                    $html .= '<ul class="tab-pane-media">
                            <li class="item-descr-title">' . $field['name'] . '</li>
                            <li class="item-descr-msg">
                                <textarea  '.$disabled.'  name="' . $name . '" id="' . $htmlId . '"
                                    style="width:450px;height:100px">' . htmlspecialchars($item->$key) . '</textarea>

                                <script type="text/javascript">
                                    var ' . $htmlId . ' = UE.getEditor("' . $htmlId . '");
                                </script>
                            </li>
                        </ul>';
                } else {
                    $html .= '<ul class="tab-pane-media">
                        <li class="item-descr-title">' . $field['name'] . '</li>
                        <li class="item-descr-msg"><input  '.$disabled.' type="text" name="' . $name . '"  style="width:360px;" value="' . $item->$key . '"/> ' . $field['desc'] . ' </li>
                        </ul>';
                }

            } elseif ($field['type'] == 'enum') {

                if (isset($field['enumDataGet']) && !isset($field['enum'])) {

                    $dataSets = $field['enumDataGet']['sets'];
                    $titleName = $field['enumDataGet']['title'];
                    $titleAddID = $field['enumDataGet']['titleAddID'];

                    $dataSets = $item->$dataSets();

                    foreach ($dataSets as $relationItem) {
                        $keyName = $field['enumDataGet']['value'] ? $field['enumDataGet']['value'] : $relationItem->getKeyName();

                        $field['enum'][$relationItem->$keyName] = $relationItem->$titleName . ($titleAddID ? ' ID:' . $relationItem->$keyName . '' : '');
                    }
                }

                if ($field['viewType'] == 'radio') {

                    $html .= '<ul class="tab-pane-media">
                                <li class="item-descr-title">' . $field['name'] . ' <span style="color:red;"></span></li>
                                <li class="item-descr-msg">';

                    foreach ($field['enum'] as $enumk => $enumv) {
                        $html .= '<input type="radio"  '.$disabled.'  name="' . $name . '" value="' . $enumk . '" id="radioset_' . $name . $enumk . '" ' . ($item->$key == $enumk ? 'checked' : '') . '><label for="radioset_' . $name . $enumk . '">' . $enumv . '&nbsp&nbsp</label>';
                    }
                    $html .= $field['desc'] . ' </li></ul>';

                } elseif ($field['viewType'] == 'select') {

                    $html .= '<ul class="tab-pane-media">
                                <li class="item-descr-title">' . $field['name'] . ' <span style="color:red;"></span></li>
                                <li class="item-descr-msg">';

                    $html .= '<select  '.$disabled.' class="chosen js-example-basic-single" data-placeholder=""  data-var="type" name="' . $name . ($field['multiple'] ? '[]' : '') . '" id="selectset_' . $key . '" ' . ($field['multiple'] ? 'multiple="multiple"' : '') . '>';

                    if (!$field['multiple']) {
                        $html .= '<option class="italic" value="" ' . (!$item->$key ? 'selected="selected" ' : '') . '> * 未选择</option>';
                        foreach ($field['enum'] as $enumk => $enumv) {
                            $html .= '<option value="' . $enumk . '" ' . ($item->$key == $enumk ? 'selected="selected"' : '') . '> - ' . $enumv . '</option>';
                        }
                    } else {
                        $keyAry = explode(',', $item->$key);
                        foreach ($field['enum'] as $enumk => $enumv) {
                            $html .= '<option value="' . $enumk . '" ' . (in_array($enumk, $keyAry) ? 'selected="selected"' : '') . '> - ' . $enumv . '</option>';
                        }
                    }

                    $html .= $field['desc'] . '</select></li></ul>';

                } else {

                    $html .= '<ul class="tab-pane-media"><li class="item-descr-title">' . $field['name'] . '</li><li class="item-descr-msg"><input '.$disabled.'  type="text" name="' . $name . '"  style="width:360px;" value="' . $item->$key . '"/>' . $field['desc'] . '</li></ul>
                    ';
                }

            }

        }

        return $html;
    }

    public static function previewContent($item)
    {

        $html = '';
        foreach (self::$autoFields as $key => $field) {

            if ($field['viewSkip']) {
                continue;
            }

            if (is_callable($field['viewClosure']) && $field['viewClosure'] instanceof \Closure) {
                $field['viewClosure']($item, $key);
            }

            if ($field['type'] == 'image') {
                $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . '</li>
                    <li class="item-descr-msg">
                        <div class="progressstatus img_view">';
                $html .= $item->$key ? '<img src="' . $item->$key . '" height="200">' : '';
                $html .= '</div>
                    </li>
                </ul>
                ';

            } elseif ($field['type'] == 'file') {
                $html .= '<ul class="tab-pane-media">
                        <li class="item-descr-title">' . $field['name'] . '</li>
                        <li class="item-descr-msg">';
                $html .= $item->$key ? '<a href="' . $item->$key . '" >' . $item->$key . '</a>' : '';
                '</li>
                    </ul>
                    ';
            } elseif ($field['type'] == 'text') {
                $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . '</li>
                    <li class="item-descr-msg">
                        ' . htmlspecialchars($item->$key) . '
                    </li>
                </ul>';

            } elseif ($field['type'] == 'enum') {

                if (isset($field['enumDataGet']) && !isset($field['enum'])) {

                    $dataSets = $field['enumDataGet']['sets'];
                    $titleName = $field['enumDataGet']['title'];
                    $titleAddID = $field['enumDataGet']['titleAddID'];

                    $dataSets = $item->$dataSets();

                    foreach ($dataSets as $relationItem) {
                        $keyName = $field['enumDataGet']['value'] ? $field['enumDataGet']['value'] : $relationItem->getKeyName();

                        $field['enum'][$relationItem->$keyName] = $relationItem->$titleName . ($titleAddID ? ' ID:' . $relationItem->$keyName . '' : '');
                    }
                }

                $html .= '<ul class="tab-pane-media">
                    <li class="item-descr-title">' . $field['name'] . ' <span style="color:red;"></span></li>
                    <li class="item-descr-msg">';
                $html .= $field['enum'][$item->$key];
                $html .= ' </li></ul>';

            }

        }

        return $html;
    }

    public static function handleCheck(int $id = 0, array $update, bool $isError = false)
    {

        $itemModel = self::getModel($id);

        foreach (self::$autoFields as $key => $field) {

            if ($field['handleSkip']) {
                continue;
            }

            if (!$field['handleCheck']) {
                continue;
            }

            if (!isset($update[$key]) || $update[$key] === '' || ($field['handleCheckZeroInt'] && $update[$key] == 0)) {
                if($isError){
                    return '[ ' . $field['name'] . ' ] 未设置/未选择/输入为空';
                }else{
                    return Admin::message('[ ' . $field['name'] . ' ] 未设置/未选择/输入为空');
                }
                
            }

            if ($field['handleCheckUnique']) {

                $idKey = self::$modelIdKey;
                $isExists = self::$model->where($key, $update[$key])
                    ->where($idKey, '!=', $itemModel->$idKey)
                    ->exists();

                if ($isExists) {
                    if($isError){
                        return '[ ' . $field['name'] . ' ] 设置的值 ' . $update[$key] . '已存在,该项必须唯一';
                    }else{
                        return Admin::message('[ ' . $field['name'] . ' ] 设置的值 ' . $update[$key] . '已存在,该项必须唯一');
                    }
                    
                }

                continue;
            }

        }
        return null;
    }

    public static function handleModel(int $id = 0, array $update)
    {

        $itemModel = self::getModel($id);

        foreach ($update as $key => $value) {

            $field = self::$autoFields[$key];

            if ($field['handleSkip']) {
                continue;
            }

            if ($field['valueTypeSet']) {
                settype($value, $field['valueTypeSet']);
            }
            if ($field['type'] == 'enum') {

                if (isset($field['enumDataGet']) && !isset($field['enum'])) {

                    $dataSets = $field['enumDataGet']['sets'];
                    $titleName = $field['enumDataGet']['title'];

                    $dataSets = $itemModel->$dataSets();

                    foreach ($dataSets as $relationItem) {
                        $keyName = $field['enumDataGet']['value'] ? $field['enumDataGet']['value'] : $relationItem->getKeyName();

                        $field['enum'][$relationItem->$keyName] = $relationItem->$titleName;
                    }
                }

                if ($field['multiple']) {
                    $value = join(',', $value);
                }

            }
            $beforeVal = null;
            if ($id) {
                $beforeVal = $itemModel->$key;
            }
            $itemModel->$key = $value;

            if (is_callable($field['handleClosure']) && $field['handleClosure'] instanceof \Closure) {
                $field['handleClosure']($itemModel, $value, $key, $field['enum'], $beforeVal);
            }

        }

        foreach (self::$autoFields as $key => $field) {
            if ($field['valueTypeSet']) {
                settype($itemModel->$key, $field['valueTypeSet']);
            }
        }

        $itemModel->save();

        return $itemModel;
    }

    public static function handleRelationModel($model, $relationModel, $updateFields, $selectField)
    {

        $relationModel::where($selectField, $model->$selectField)
            ->chunkById(50000, function ($list) use ($updateFields, $model) {
                foreach ($list as $one) {
                    foreach ($updateFields as $field) {
                        $one->$field = $model->$field;
                    }
                    $one->save();
                }
            });
    }

    public static function getModel(int $id = 0)
    {

        if ($id) {
            $itemModel = self::$model::find($id);
        } else {
            $itemModel = self::$model->replicate();
        }

        viewAssign('item', $itemModel);

        return $itemModel;
    }

    public static function modelArray($model, $key = '')
    {
        if ($key) {
            return $model->$key;
        } else {
            return $model->toArray();
        }

    }

    public static function dateToTimestamp($model, $fieldValue = '', $fieldKey = '', $autoFillKey = '')
    {

        $model->$autoFillKey = $fieldValue != '' ? strtotime($fieldValue) : 0;
        $model->$fieldKey = $fieldValue != '' ? $fieldValue : '0000-00-00 00:00:00';
        return $model;
    }

    public static function globalViewAssign()
    {

        $args = func_get_args();

        foreach ($args as $oneArg) {
            foreach ($oneArg as $key => $val) {
                $$key = $val;
            }
        }

        $adminUserAutModel = new AdminUserAuthModel();

        $menus = [];


        $menus['Goods'] = [
            'name' => '商品管理',
            'subMenus' => [
                'list' => '商品列表',
            ],
        ];

        $menus['Supplier'] = [
            'name' => '供贸商管理',
            'subMenus' => [
                'list' => '供贸商管理',
            ],
        ];


        $menus['Order'] = [
            'name' => '订单管理',
            'subMenus' => [
                'list' => '订单列表',
            ],
        ];

        $menus['AdminUser'] = [
            'name' => '用户登录管理',
            'subMenus' => [
                'list' => '用户登录管理',
            ],
        ];

        //$this->request->vars

        viewAssign('requestVars', ff::$app->router->request->vars);
        viewAssign('menus', $menus);
        viewAssign('paginateShowlinkSize', self::PAGINATE_SHOWLINK_SIZE);
        viewAssign('menuCur', ff::$app->router->urlManager->_CONTROLLER);
        viewAssign('auid', $adminUserAutModel->auid);
        viewAssign('username', $adminUserAutModel->username);
        viewAssign('baseurl', constant('RUNTIME_HTTP_HOST'));

    }

    public static function makeNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }
}
