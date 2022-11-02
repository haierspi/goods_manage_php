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
use models\tables\BlockchainModel;
use models\tables\BrandModel;
use models\tables\ContractMetadataModel;
use models\tables\ContractTemplateModel;
use models\tables\CopyrightModel;
use models\tables\GoodsCategoryModel;
use models\tables\GoodsModel;
use models\tables\MemberModel;
use models\tables\OrderModel;
use models\tables\ReleaseModel;
use models\tables\TopicGoodsModel;
use XiangYu2038\WithXy\condition\AndCondition;
use XiangYu2038\WithXy\condition\Condition;
use XiangYu2038\WithXy\condition\LikeCondition;

class GoodsController extends CookieAuthController
{
    private $pageNum = 20;
    private $modelClass = '\models\tables\GoodsModel';

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
     *      searchTextLike        需要LIKE搜索 
     *      searchClosure 自定义
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

        $user = $this->user;

        if($act == ''){
            $this->autoFields = [
                'goods_id' => ['name' => 'ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id', 'search' => 1],
                'goods_sn' => ['name' => '货号', 'type' => 'text',
                    'viewClosure' => function ($model) {

                        if (!$model->goods_id || !$model->goods_sn) {
                            $model->goods_sn = Admin::makeNo();
                        }
                    },
                    'search' => 1,
                ],
                'auid' => ['name' => '后端发布用户ID', 'type' => 'text', 'listSkip' => 1, 'viewType' => 'hidden', 'viewClosure' => function ($model) use ($user) {
                    if (!$model->auid) {
                        $model->auid = $user->auid;
                    }
                }],
    
                'category_id' => ['name' => '分类', 'type' => 'enum', 'search' => 1, 'listSkip' => 1,
                    'enumDataGet' => ['set' => 'goodsCategory', 'sets' => 'goodsCategorys', 'title' => 'category_name'],
                    'viewType' => 'select',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                    'handleClosure' => function ($model, $id) {
                        $goodsCategory = GoodsCategoryModel::find($id);
                        $model->category_name = $goodsCategory->category_name;
                    },

                ],
                'category_name' => ['name' => '分类', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],

                'topic_ids' => ['name' => '专题', 'type' => 'enum',
                    'listSkip' => 1,
                    'enumDataGet' => ['sets' => 'topics', 'title' => 'topic_name', 'titleAddID' => true],
                    'viewType' => 'select',
                    'multiple' => 1,
                    'search' => 1,
                    'searchTextLike'=>1,
                    'searchClosure' => function ($value) {
                        return ','.$value.',';
                    },
                    
                    'handleClosure' => function ($model, $values, $key, $mdlist, $beforVal) {
    
                        if (!$model->goods_id) {
                            $model->save();
                        }

                        if ($model->goods_id) {
                          TopicGoodsModel::where('goods_id', $model->goods_id)->delete();
                        }

                        $ids = explode(',',$values);
                        foreach ( $ids as $topicId) {
                            if($topicId){
                                $sm = new TopicGoodsModel;
                                $sm->goods_id = $model->goods_id;
                                $sm->topic_id = $topicId;
                                $sm->weight = $model->weight;
                                $sm->save();
                            }
                        }
                        $model->$key = ','.$values.',';
                    }
                ],


                'copyright_id' => ['name' => '版权方', 'type' => 'enum', 'enumDataGet' => ['set' => 'copyright', 'sets' => 'copyrights', 'title' => 'copyright_name'], 'listSkip' => 1,
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $copyright = CopyrightModel::find($id);
                        $model->copyright_name = (string) $copyright->copyright_name;
                        $model->copyright_image = (string) $copyright->copyright_image;
                    },
                ],
                'copyright_name' => ['name' => '版权方名字', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'brand_id' => ['name' => '品牌', 'type' => 'enum', 'listSkip' => 1, 'enumDataGet' => ['set' => 'brand', 'sets' => 'brands', 'title' => 'brand_name'],
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $brand = BrandModel::find($id);
                        $model->brand_name = (string) $brand->brand_name;
                    },
                ],
    
                'brand_name' => ['name' => '品牌方名称', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'release_id' => ['name' => '发行方', 'type' => 'enum', 'listSkip' => 1, 'enumDataGet' => ['set' => 'release', 'sets' => 'releases', 'title' => 'release_name'],
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $release = ReleaseModel::find($id);
                        $model->release_name = (string) $release->release_name;
                    },
                ],
    
                'release_id' => ['name' => '发行方', 'type' => 'enum', 'listSkip' => 1, 'enumDataGet' => ['set' => 'release', 'sets' => 'releases', 'title' => 'release_name'],
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $release = ReleaseModel::find($id);
                        $model->release_name = $release->release_name;
                    },
                ],
    
                'release_name' => ['name' => '发行方名称', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
    


                'goods_type' => ['name' => ' 商品类型 ', 'type' => 'enum', 'enum' => ['0' => '数字+实物商品', '1' => '数字藏品', '2' => '实物礼品', '3' => '数字藏品-盲盒'], 'viewType' => 'select', 'search' => 1],
    
                'contract_template_id' => ['name' => ' 合约模板 ', 'type' => 'enum', 'enumDataGet' => ['set' => 'contractTemplate', 'sets' => 'contractTemplates', 'title' => 'title', 'titleAddID' => true],
                    'viewType' => 'select',
                    'handleClosure' => function ($model, $id) {
                        $t = ContractTemplateModel::find($id);
                        $model->blockchain_id = $t->blockchain_id;
                        $model->blockchain_name = $t->blockchain_name;
                        $model->blockchain_key = $t->blockchain_key;
                        $model->blockchain_icon = $t->blockchain_icon;
                        $model->blockchain_address = $t->blockchain_address;
                        $model->contract_type = $t->contract_type;
                        $model->contract_network = $t->contract_network;
                        $model->contract_tokenuri_url_domain = $t->contract_tokenuri_url_domain;
                        $model->contract_tokenuri_url_pre = $t->contract_tokenuri_url_pre;
                        $model->contract_keystore_path = $t->contract_keystore_path;
                    },
                    'search' => 1,
                ],
    
                'blockchain_name' => ['name' => '公链类型', 'type' => 'text', 'viewSkip' => 1],
                'blockchain_address' => ['name' => '合约地址', 'type' => 'text', 'listSkip' => 1, 'viewSkip' => 1],
                'contract_metadata_id' => ['name' => '合约metadataID', 'type' => 'enum',
                    'enumDataGet' => ['set' => 'contractMetadata', 'sets' => 'contractMetadatas', 'title' => 'contract_metadata_name', 'titleAddID' => true],
                    'viewType' => 'select',
                    'multiple' => 1,
                    'handleClosure' => function ($model, $value, $key, $mdlist) {
    
                        if (!$model->goods_id) {
                            $model->save();
                        }
    
                        $ids = explode(',', $value);
    
                        if ($model->goods_type != 3 && count($ids) > 1) {
                            Admin::exit_message(" 非 [ 数字藏品-盲盒 ] 商品类型的商品不能关联多个MetadataID");
                        }
    
                        $list = ContractMetadataModel::whereIn('contract_metadata_id', $ids)->select('contract_metadata_id', 'contract_metadata_name', 'goods_id')->get();
    
                        $metadataIds = [];
                        if ($list) {
                            foreach ($list as $one) {
                                if ($one->goods_id != 0 && $one->goods_id != $model->goods_id) {
                                    Admin::exit_message("Metadata 设置失败, MetadataID:" . $one->contract_metadata_id . "已经关联了其他商品");
                                }
                            }
    
                            foreach ($list as $one) {
                                unset($mdlist[$one->contract_metadata_id]);
                                $metadataIds[] = $one->contract_metadata_id;
                                $one->goods_id = $model->goods_id;
                                $one->save();
                            }
    
                        }
    
                        if ($mdlist) {
                            ContractMetadataModel::whereIn('contract_metadata_id', array_keys($mdlist))->update(['goods_id' => 0]);
                        }
    
                        $model->$key = join(',', $metadataIds);
                    }
                ],
    
                'is_hide' => ['name' => '是否隐藏', 'type' => 'enum', 'enum' => ['0' => '显示', '1' => '隐藏'], 'viewType' => 'radio', 'search' => 1],
                'status' => ['name' => '商品状态', 'type' => 'enum', 'enum' => ['0' => '已下架', '1' => '上架中'], 'viewType' => 'radio', 'search' => 1],
    

    
                

                'goods_name' => ['name' => '商品名称', 'type' => 'text', 'search' => 1, 'searchTextLike' => 1],
                'goods_url' => ['name' => 'URL Tag', 'type' => 'text',
                    'listClosure' => function ($model, $key) {
                        $model->$key = '<a href="https://m.starfission.com/goods/' . $model->$key . '" target="_blank">' . $model->$key . '</a>';
                    },
                    'handleCheck' => 1,
                    'handleCheckUnique' => 1,
                ],
    
                'goods_price' => ['name' => '商品价格', 'type' => 'text', 'valueTypeSet' => 'float'],
                'goods_market_price' => ['name' => '市场价', 'type' => 'text', 'listSkip' => 1, 'valueTypeSet' => 'float'],
                'goods_stock' => ['name' => '当前库存', 'type' => 'text', 'valueTypeSet' => 'int'],
                'goods_total_stock' => ['name' => '发行总量', 'type' => 'text', 'valueTypeSet' => 'int'],
                'buy_num_limit' => ['name' => '限购数量', 'type' => 'text', 'valueTypeSet' => 'int'],
                
                'released_at' => ['name' => '发行时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'released_time');
                    },
                ],
                'sale_at' => ['name' => '销售时间', 'type' => 'text', 'viewType' => 'time',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'sale_time');
                    },
                ],
                'weight' => ['name' => '排序', 'valueTypeSet' => 'int', 'type' => 'text', "desc" => "越大越靠前"],

                'goods_express_type' => ['name' => '运费类型', 'type' => 'enum', 'enum' => ['0' => '免运费', '1' => '全国运费', '2' => '距离计价'], 'listSkip' => 1, 'viewType' => 'radio'],
    
                'goods_express_price' => ['name' => '商品运费 0 免运费', 'type' => 'text', 'listSkip' => 1, 'valueTypeSet' => 'float'],
                'goods_weight' => ['name' => '商品单位重量（g）', 'type' => 'text', 'listSkip' => 1, 'valueTypeSet' => 'int'],

                'goods_title_pic' => ['name' => '标题图', 'type' => 'image', 'property' => 'width="50"',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                ],
                'goods_thumb_pic' => ['name' => '缩略图', 'type' => 'image', 'property' => 'width="50"',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                ],
                'goods_image' => ['name' => '商品图', 'type' => 'image', 'property' => 'width="50"',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                ],
                'goods_ar' => ['name' => 'ar 模型', 'type' => 'image', 'listSkip' => 1],
                'goods_ar_image' => ['name' => 'ar加载图', 'type' => 'image', 'listSkip' => 1],
                'goods_tags' => ['name' => '标签', 'desc' => '使用|逗号间隔', 'type' => 'text'],
                'goods_body' => ['name' => '商品内容', 'type' => 'text', 'viewType' => 'html', 'listSkip' => 1],
                'goods_body_mobile' => ['name' => '商品内容移动版', 'type' => 'text', 'viewType' => 'html', 'listSkip' => 1],
    
    
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1],
            ];
    
            $this->autoRewardCodeFields = [
    
                'id' => ['name' => 'ID', 'type' => 'text', 'viewSkip' => 1],
    
                'code' => ['name' => '兑换码', 'type' => 'text', 'viewSkip' => 1],
    
                'goods_id' => ['name' => '绑定商品ID', 'type' => 'text', 'handleCheck' => 1],
    
                'uid' => ['name' => '绑定用户', 'type' => 'text',
                    'listClosure' => function ($model, $key) {
                        if ($model->$key) {
                            $model->$key = '<a href="/Member/List?uid=' . $model->member->uid . '" target="_blank">' . $model->member->nickname . '</a>';
                        } else {
                            $model->$key = '';
                        }
                    },
                    'desc' => '如果需要绑定用户请输入绑定用户UID, 不绑定请留空',
                ],
    
                'remain_qty' => ['name' => '剩余次数', 'type' => 'text', 'handleCheck' => 1, 'desc' => '使用兑换券会扣减这个数量,扣到0后兑换券失效'],
    
                'is_used' => ['name' => '是否使用', 'type' => 'enum', 'enum' => ['0' => '未使用', '1' => '已使用'], 'viewSkip' => 1, 'search' => 1],
    
                'is_deleted' => ['name' => '是否失效', 'type' => 'enum', 'enum' => ['0' => '生效中', '1' => '已失效'], 'viewSkip' => 1, 'search' => 1],
    
                'used_at' => ['name' => '使用时间', 'type' => 'text', 'viewSkip' => 1, 'viewType' => 'time'],
    
                'batch_id' => ['name' => '批次ID', 'type' => 'text', 'search' => 1],
    
                'start_at' => ['name' => '生效开始时间', 'type' => 'text', 'viewType' => 'time', 'desc' => '留空不限制',
    
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'start_at');
                    },
    
                ],
                'end_at' => ['name' => '生效结束时间', 'type' => 'text', 'viewType' => 'time', 'desc' => '留空不限制',
                    'handleClosure' => function ($model, $fieldValue, $fieldKey) {
                        return Admin::dateToTimestamp($model, $fieldValue, $fieldKey, 'end_at');
                    },
                ],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1, 'handleSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1, 'handleSkip' => 1],
    
                'creat_num' => ['name' => '生成兑换券个数', 'type' => 'text', 'handleCheck' => 1, 'handleSkip' => 1, 'listSkip' => 1, 'desc' => '生成张数, 绑定用户请输入1'],
            ];
        } else if ($act == 'topic') {
            $this->autoFields = [
                'topic_id' => ['name' => '专题ID', 'type' => 'text', 'viewType' => 'hidden', 'updateTypeId' => 'id'],
                'category_id' => ['name' => '所属分类ID', 'type' => 'enum', 'search' => 1, 
                    'enumDataGet' => ['set' => 'goodsCategory', 'sets' => 'goodsCategorys', 'title' => 'category_name'],
                    'viewType' => 'select',
                ],
                'topic_name' => ['name' => '专题名称', 'type' => 'text'],
                'topic_url' => ['name' => 'URL Tag', 'type' => 'text', 'desc' => 'URL关键词'],
                'topic_image' => ['name' => '专题标题图', 'type' => 'image', 'property' => 'width="50"',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                ],

                'topic_banner' => ['name' => '专题背景图', 'type' => 'image', 'property' => 'width="50"',
                    'listClosure' => function ($model, $key) {
                        $model->$key = $model->$key . '?x-oss-process=image/resize,m_lfit,w_100/quality,q_80';
                    },
                ],


                'topic_desc' => ['name' => '专题文字描述','type' => 'text', 'viewType' => 'textarea'],
                'redirect_url' => ['name' => '强制跳转地址', 'type' => 'text'],
                'is_hide' => ['name' => '是否隐藏', 'type' => 'enum', 'enum' => ['0' => '显示', '1' => '隐藏'], 'viewType' => 'radio', 'search' => 1],
                'weight' => ['name' => '排序', 'valueTypeSet' => 'int', 'type' => 'text', "desc" => "越大越靠前"],
                'created_at' => ['name' => '创建时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1],
                'updated_at' => ['name' => '更新时间', 'type' => 'text', 'viewSkip' => 1, 'listSkip' => 1],
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

        }



    }

    public function actionList()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\GoodsModel';
        $itemName = '商品列表';

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
                    'url' => '/' . $this->controllerPath . '/update',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/update?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
                "兑换码" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/RewardCodeList?goods_id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
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

        $update['contract_metadata_id'] = (array) $update['contract_metadata_id'];

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

            Redis::dels("MALL_GOODSLIST_*");
            Redis::del("MALL_GOODSTOTAL");
            Redis::del("MALL_GOODSINFOURL_" . $itemModel->goods_url);

            return Admin::message($id ? $itemName . ' 编辑成功' : $itemName . ' 新建成功', '/' . $this->actionPath . '?id=' . $itemModel->$idKey);

        } else {

            Admin::getModel($id);
            Admin::globalViewAssign();

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }

    public function actionRewardCodeList()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\RedeemCodeModel';

        $goodsModel = GoodsModel::find($this->request->vars['goods_id']);

        $itemName = '<a href="/Goods/list">商品列表</a> &gt; <a href="/Goods/list?goods_id=' . $goodsModel->goods_id . '">' . $goodsModel->goods_name . '</a>  &gt; 兑换券';

        $page = empty($this->request->vars['page']) ? 1 : (int) $this->request->vars['page']; //页码
        $pageNum = empty($this->request->vars['page_num']) ? $this->pageNum : $this->request->vars['page_num']; //每页数据量

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoRewardCodeFields);
        Admin::globalViewAssign();

        viewAssign('goodsModel', GoodsModel::find($this->request->vars['goods_id']));

        $idKey = Admin::$modelIdKey;

        $list = Admin::$model::whereHas(Admin::listSearchCondition())
            ->where('goods_id', $goodsModel->goods_id)
            ->orderBy($idKey, 'DESC')
            ->paginate($pageNum, $columns = ['*'], $pageName = 'page', $page)
            ->setPath(Admin::listPaginateURL());

        //新增按钮
        Admin::listAddButtons(
            [
                "生成兑换券" => [
                    'url' => '/' . $this->controllerPath . '/BatchGenerate?goods_id=' . $goodsModel->goods_id,
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "强制失效" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/RewardCodeDel?id=' . $model->$idKey;
                    },
                    'class' => 'btn-danger',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionRewardCodeDel()
    {

        $this->initAutoFields();
        $modelClass = '\models\tables\RedeemCodeModel';

        $itemName = '兑换券';
        $listURL = '/News/VersionList';

        Admin::setModel($modelClass);

        $model = Admin::$model::find($this->request->vars['id']);
        $model->is_deleted = 1;
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->save();

        return Admin::message($itemName . ' 已经设置为失效', $listURL);
    }

    public function actionBatchGenerate()
    {

        $this->initAutoFields();

        $modelClass = '\models\tables\RedeemCodeModel';
        $goodsModel = GoodsModel::find($this->request->urlVars['goods_id']);
        $itemName = '<a href="/Goods/list">商品列表</a> &gt; <a href="/Goods/list?goods_id=' . $goodsModel->goods_id . '">' . $goodsModel->goods_name . '</a>  &gt; 生成兑换券';

        $id = (int) $this->request->vars['id'];
        $update = empty($this->request->vars['update']) ? [] : $this->request->vars['update'];

        Admin::setModel($modelClass);
        Admin::setAutoFields($this->autoRewardCodeFields);

        if ($_POST) {

            //输入检查
            $error = Admin::handleCheck($id, $update);
            if ($error != null) {
                return $error;
            }
            $update['uid'] = (int) $update['uid'];

            $codes = [];
            do {
                $codes[] = 'CDX' . date('YmdHis') . strtoupper(uniqid());
            } while (count($codes) < $update['creat_num']);

            Admin::globalViewAssign();

            $modelList = [];
            $batchId = $update['batch_id'];

            foreach ($codes as $key => $code) {

                $update['code'] = $code;
                $update['batch_id'] = $batchId;

                $model = Admin::handleModel(0, $update);
                if ($key == 0 && !$batchId) {
                    $batchId = $model->batch_id = 'BATCH-ID-' . $model->id;
                    $model->save();
                }

                $modelList[] = $model;

            }

            return Admin::message("批量生成成功", '/Goods/RewardCodeList?goods_id=' . $goodsModel->goods_id);

        } else {

            $itemModel = Admin::getModel($id);
            Admin::globalViewAssign();

            $itemModel->goods_id = $goodsModel->goods_id;
            $itemModel->remain_qty = 1;
            $itemModel->creat_num = 1;

            viewAssign('pageTitle', $itemName);
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }


    public function actionGoodsCategoryList()
    {
        $this->initAutoFields('goodsCategory');
        $modelClass = '\models\tables\GoodsCategoryModel';
        $itemName = '商品分类';

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
                    'url' => '/' . $this->controllerPath . '/GoodsCategoryUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/GoodsCategoryUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');
    }

    public function actionGoodsCategoryUpdate()
    {

        $this->initAutoFields('goodsCategory');
        $modelClass = '\models\tables\GoodsCategoryModel';
        $itemName = '商品分类';

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

    public function actionTopicList()
    {

        $this->initAutoFields('topic');

        $modelClass = '\models\tables\TopicModel';
        $itemName = '专题列表';

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
                    'url' => '/' . $this->controllerPath . '/TopicUpdate',
                    'class' => 'btn-danger',
                ],
            ]
        );

        //操作按钮
        Admin::listOperateButtons(
            [
                "编辑" => [
                    'url' => function ($model) use ($idKey) {
                        return '/' . $this->controllerPath . '/TopicUpdate?id=' . $model->$idKey;
                    },
                    'class' => 'btn-primary',
                ],
            ]
        );

        viewAssign('list', $list);
        viewAssign('pageTitle', $itemName);

        return viewfile('cms/common/list');

    }

    public function actionTopicUpdate()
    {

        $this->initAutoFields('topic');

        $modelClass = '\models\tables\TopicModel';
        $itemName = '专题';

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

            viewAssign('pageTitle', $id ? $itemName . '编辑' : $itemName . '新建');
            viewAssign('date', date('Y-m-d'));
            return viewfile('cms/common/update');
        }
    }

}
