<?php
namespace common;

use ff\code\ErrorCode as ErrorCodeFF;

/**
 * 应用级错误代码
 */
class ErrorCode extends ErrorCodeFF
{

    const NO_PERMISSION_OPERATE = -1013; // You do not have permission to operate;
    const ASSOCIATED_USER_EXIST = -1014; // The associated user is not empty;
    const USER_EXIST = -1015; // User exists;
    const ROLE_NOT_EXIST = -1016; // Role does not exist;
    const WRONG_ROLE = -1017; // You have chosen the wrong role;
    const DELETE_YOURSELF = -1018; // You cannot delete yourself;
    const NICKNAME_EXIST = -1019; // Nickname exists;
    const USER_NOT_EXIST = -1020; // User does not exist;
    const ROLENAME_EXIST = -1021; // Rolename exists;

    const GOODS_SKU_EXIST = -2003; //goods sku does not exist
    const USER_ERROR = -2004; //用户不存在或被禁用！
    const PASSWORD_ERROR = -2005; //密码不正确
    const USER_NO_PERMISSION = -2006; //您没有操作权限

    
    const BLOCK_WORD_EXIST = -4001; //屏蔽关键字已经存在
    const BLOCK_WORD_NOT_EXIST = -4002; //屏蔽关键字不存在
    const APP_NOT_EXIST = -4003; //应用不存在
    const BLOCK_WORD_ERROR = -4004; //屏蔽关键字不能含有百分号(%)或下横线(_)
    const COMMENT_NOT_EXIST = -4005; //该评论不存在
    const COMMENT_UNABLE_DISPLAY_OPERATE = -4006; //该评论不能被设置隐藏/显示操作
    const COMMENT_WAIT_REPEAT_OPERATE = -4007; //该评论上一次操作尚未执行完毕,执行期间不能再次操作

    const SAVE_FAIL = -5001; //保存失败
    const NO_DATA = -5002; //无数据
    const OPERATE_FAIL = -5003; //操作失败
    const USER_EXIST_DATA = -5004; //该开发员已存在数据
    const POSITION_EXIST = -5005; //部位名称已存在
    const REQUEST_MODE_ERROR = -5101; //请求方式错误
    const DUPLICATE_NAME = -5102; //名称重复

    const UPLOAD_FILEEXT_ERROR = -1030; //上传附件类型错误
    const IMPORT_EXCEL_EXT_ERROR = -1031; //上传附件类型错误


    const SUPPLIER_NOT_EXIST = -6001; //对不起，您开发的供应商不存在，请创建供应商。
    const SUPPLIER_PRODUCT_SN_MONTH_MAX_NUM_LIMIT = -6002; //对不起，您开发的供应商本月上新已到达上限。请与供应链联系升级或下月再进行开发。

    const UPDATE_SUPPLIER_STATUS_FAILED = -6004; //对不起，供应商状态修改失败
    const UPDATE_SUPPLIER_TYPE_FAILED = -6005; //对不起，供应商分类修改失败
    const NO_RELATED_INFORMATION_CAN_BE_FOUND = -6006; //查询不到相关关联信息
    const GOODS_PURCHASE_URL_NOT_EXIST = -6007;
    const SUPPLIER_USER_NOT_RELATED = -6008; //供应商用户没有关联关系
    const ALIBABA_PRODUCT_URL_INVALID = -6009; //阿里巴巴1688商品连接不存在或获取商品信息异常
    const SUPPLIER_ALIBABA_PRODUCT_NOT_MATCH = -6010; //供应商和商品连接不匹配
    const SUPPLIER_STATUS2_FAILED = -6011; //请选择简称-三档-1688接单的供应商。该供应商已被淘汰请联系供应链修改或放弃开发。
    const SUPPLIER_STATUS3_FAILED = -6012; //请选择简称-三档-1688接单的供应商。该供应商已淘汰请联系供应链修改或放弃开发。
    const SUPPLIER_ALIBABA_PRODUCT_MATCH_STATUS_FAILED = -6013;
    const SUPPLIER_ALIBABA_PRODUCT_MATCH_STATUS_FAILED1= -6013;

    const HUB_SUPPLIER_ORDER_NOT_EXIST = -7001; //订单中心采购订单不存在
    const HUB_SUPPLIER_ORDER_NOT_PUSHED = -7002; //订单中心采购订单没有推送不能取消
    const HUB_SUPPLIER_ORDER_DELETED = -7003; //订单中心采购订单已经被删除
    const HUB_SUPPLIER_SELECT_TIME_TOO_LONG = -7004; //查询时间不能超过7天

    const HUB_IMPORT_STORE_NOT_EXIST = -7005; //仓库不存在
    const HUB_IMPORT_TITLE_FAIL = -7006; //导入类型名称不正确
    const HUB_IMPORT_TITLE_EXIST = -7007; //导入类型名称已经存在

    const GOODS_NEED_SAMPLING = -8001; //商品需要采样但无采样记录
    const GOODS_IN_SAMPLING = -8002; //商品正在采样中
    const GOODS_SAMPLING_FAIL = -8003; //商品采样失败
    const GOODS_SAMPLING_AGAIN = -8004; //商品采样失败已重新发起

    const TESTING_REPORT_FAIL = -8501; //对不起，供应商用户不存在。

    const SUPPLIER_USER_NOT_EXIST = -8001; //对不起，供应商用户不存在。
    const SUPPLY_CHAIN_QUERIES_NUMBER_LIMIT_ERROR = -8002; 
    
    const SUPPLIERS_DUPLICATE_NAME = -8003; //供应商简称重复

    const UPLOAD_GOODS_ID_EXT = -8005; //上传文件格式不正确

    const DEMAND_PLAN_SET_GOODS_STOCK_QTY_ERROR = -7101; //设置商品整款需求数量失败,请检查商品是否存在
    const DEMAND_PLAN_SET_SKU_STOCK_QTY_ERROR = -7102; //设置SKU需求数量失败,请检查SKU是否存在

    


    // const   =    -5103; //参数错误
    //  const  _MSG =    '参数错误';

    /**
     *  MSG Content
     */



    const NO_PERMISSION_OPERATE_MSG = 'You do not have permission to operate;';
    const ASSOCIATED_USER_EXIST_MSG = 'The associated user is not empty;';
    const USER_EXIST_MSG = 'User exists;';
    const ROLE_NOT_EXIST_MSG = 'Role does not exist;';
    const WRONG_ROLE_MSG = 'You have chosen the wrong role;';
    const DELETE_YOURSELF_MSG = 'You cannot delete yourself;';
    const NICKNAME_EXIST_MSG = 'Nickname exists;';
    const USER_NOT_EXIST_MSG = 'User does not exist;';
    const ROLENAME_EXIST_MSG = 'Rolename exists;';
    const GOODS_SKU_EXIST_MSG = 'goods sku does not exist';
    const USER_ERROR_MSG = '屏蔽关键字已经存在';
    const PASSWORD_ERROR_MSG = '密码不正确';
    const USER_NO_PERMISSION_MSG = '您没有操作权限';

    


    const BLOCK_WORD_EXIST_MSG = '屏蔽关键字已经存在';
    const BLOCK_WORD_NOT_EXIST_MSG = '屏蔽关键字不存在';
    const APP_NOT_EXIST_MSG = '应用不存在';
    const BLOCK_WORD_ERROR_MSG = '屏蔽关键字不能含有百分号(%)或下横线(_)';
    const COMMENT_NOT_EXIST_MSG = '该评论不存在';
    const COMMENT_UNABLE_DISPLAY_OPERATE_MSG = '该评论不能被设置隐藏/显示操作';
    const COMMENT_WAIT_REPEAT_OPERATE_MSG = '该评论上一次操作尚未执行完毕,执行期间不能再次操作';
    const SAVE_FAIL_MSG = '保存失败';
    const NO_DATA_MSG = '无数据';
    const OPERATE_FAIL_MSG = '操作失败';
    const USER_EXIST_DATA_MSG = '该开发员已存在数据';
    const POSITION_EXIST_MSG = '部位名称已存在';
    const REQUEST_MODE_ERROR_MSG = '请求方式错误';
    const DUPLICATE_NAME_MSG = '名称重复';

    const UPLOAD_FILEEXT_ERROR_MSG  ='上传文件类型错误';
    const IMPORT_EXCEL_EXT_ERROR_MSG  ='导入EXCEL文件类型错误';

    const SUPPLIER_NOT_EXIST_MSG = '您选择的供应商不存在，请先创建后提交。'; //对不起，您开发的供应商不存在，请创建供应商。
    const SUPPLIER_PRODUCT_SN_MONTH_MAX_NUM_LIMIT_MSG = '对不起，您开发的供应商本月上新已到达上限。请与供应链联系升级或下月再进行开发。'; //
    //const SUPPLIER_STATUS_FAILED_MSG = '对不起，您开发的供应商状态异常, 请联系供应链检查'; //对不起，您开发的供应商不存在，请创建供应商。
    const UPDATE_SUPPLIER_STATUS_FAILED_MSG = '对不起，供应商状态修改失败';
    const UPDATE_SUPPLIER_TYPE_FAILED_MSG = '对不起，供应商分类修改失败';
    const NO_RELATED_INFORMATION_CAN_BE_FOUND_MSG = '查询不到相关关联信息';//查询不到相关关联信息
    const GOODS_PURCHASE_URL_NOT_EXIST_MSG = '1688商品网址不正确'; 
    const SUPPLIER_USER_NOT_RELATED_MSG = '供应商用户没有关联关系'; 
    const ALIBABA_PRODUCT_URL_INVALID_MSG = '阿里巴巴1688商品链接不存在或获取商品信息异常'; //阿里巴巴1688商品连接不存在或获取商品信息异常
    const SUPPLIER_ALIBABA_PRODUCT_NOT_MATCH_MSG = '您选择的供应商不正确，请选择 简称-三档-1688接单'; //供应商和商品连接不匹配
    const SUPPLIER_STATUS2_FAILED_MSG = '该供应商已黑名单，请联系供应链修改或放弃开发。'; 
    const SUPPLIER_STATUS3_FAILED_MSG = '该供应商已淘汰，请联系供应链修改或放弃开发。'; //
    const SUPPLIER_ALIBABA_PRODUCT_MATCH_STATUS_FAILED_MSG = '很抱歉,1688商品采购连接对应的供应商状态异常,请放弃或联系供应链修改';
    

    const HUB_SUPPLIER_ORDER_NOT_EXIST_MSG = '订单中心采购订单不存在'; //订单中心采购订单不存在
    const HUB_SUPPLIER_ORDER_NOT_PUSHED_MSG = '订单中心采购订单没有推送不能取消'; //订单中心采购订单没有推送不能取消
    const HUB_SUPPLIER_ORDER_DELETED_MSG = '订单中心采购订单已经被取消过了,不能重复操作'; //订单中心采购订单已经被删除

    const HUB_SUPPLIER_SELECT_TIME_TOO_LONG_MSG = '查询时间不能超过7天';
    const HUB_IMPORT_STORE_NOT_EXIST_MSG  = '仓库不存在'; //仓库不存在
    const HUB_IMPORT_TITLE_FAIL_MSG = '导入类型名称不正确'; //导入类型名称不正确
    const HUB_IMPORT_TITLE_EXIST_MSG  = '导入类型名称已经存在'; //导入类型名称已经存在

    
    const GOODS_NEED_SAMPLING_MSG = '商品需要采样但无采样记录,已经发起采样,需要采样成功才能上架'; //商品需要采样但无采样记录
    const GOODS_IN_SAMPLING_MSG = '商品正在采样中,需要采样成功才能上架'; //商品正在采样中
    const GOODS_SAMPLING_FAIL_MSG = '商品采样失败,不可上架'; //商品采样失败
    const GOODS_SAMPLING_AGAIN_MSG = '商品采样失败已重新发起'; //商品采样失败已重新发起

    const TESTING_REPORT_FAIL_MSG = '采购单及快递单错误，请校验采购单及快递单'; //采购单及快递单错误，请校验采购单及快递单

    const SUPPLIER_USER_NOT_EXIST_MSG = '对不起，供应商用户不存在。'; //对不起，供应商用户不存在。
    const SUPPLY_CHAIN_QUERIES_NUMBER_LIMIT_ERROR_MSG = '供应链库存查询个数限制错误'; 
    const SUPPLIERS_DUPLICATE_NAME_MSG = '供应商简称重复'; //供应商简称重复

    const UPLOAD_GOODS_ID_EXT_MSG = '上传文件格式必须是zip'; //上传文件格式不正确



    
    const DEMAND_PLAN_SET_GOODS_STOCK_QTY_ERROR_MSG= '设置商品整款需求数量失败,请检查商品是否存在'; //
    const DEMAND_PLAN_SET_SKU_STOCK_QTY_ERROR_MSG = '设置SKU需求数量失败,请检查SKU是否存在'; //


}
