<?php

return [


    'pager' => [
        'title' => '分页数据',
        'type' => 'object',
        'data' => [
            'first_page_url'=> "(API不用使用)",
            'from'=> '(API不用使用)',
            'last_page'=> '共含有页数',
            'last_page_url'=> "(API不用使用)",
            'next_page_url'=> '(API不用使用)',
            'path'=> "(API不用使用)",
            'per_page'=> '当前每页显示数量',
            'prev_page_url'=> '(API不用使用)',
            'to'=> '(API不用使用)',
            'total'=> '信息数量总计',
        ],
    ],
    'bannerData' => [
        'title' => 'banner推荐',
        'type' => 'object',
        'data' => [
            'id' => '推荐ID',
            'title' => '标题',
            'image' => '标题图',
            'type' => '类型 0 广告赞助商 1 公园售票',
            'content' => '详情 ',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'deleted_at' => '标记删除时间',
            'order' => '排序',
        ],
    ],


    'recommendData' => [
        'title' => '限时推荐列表',
        'type' => 'object',
        'data' => [
            'id' => '推荐ID',
            'title' => '标题',
            'image' => '标题图',
            'type' => '类型 0 藏品NFT 1 公园售票',
            'type_id' => '类型关联ID 比如NFT 那么就是 NFT的ID',
          
            'start_at' => '开始时间 - 每日零时',
            'end_at' => '结束时间- 当日某个时刻时间',
            
            'content' => '详情 html代码',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'deleted_at' => '标记删除时间',
            'order' => '排序',
        ],
    ],


    'articleCategoryData' => [
        'title' => '文章分类列表',
        'type' => 'object',
        'data' => [
            'cate_id' => '分类ID',
            'title' => '分类标题',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'order' => '排序',
          
          
        ],
    ],


    'articleData' => [
        'title' => '数字杂志列表',
        'type' => 'object',
        'data' => [
            'id' => '文章ID',
            'title' => '标题',
            'cate_id' => '分类ID',
            'type' => '类型 0 藏品NFT 1 公园售票',
            'type_id' => '类型关联ID 比如NFT 那么就是 NFT的ID',
            'image' => '标题图',
            'video' => '视频',
            'content' => '详情 html代码 ',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'deleted_at' => '标记删除时间',
            'order' => '排序',
          
        ],
    ],

  
];
