<?php
namespace common\listeners;

use common\event\Verify;
use common\logicalentity\DingDingServer;
use common\logicalentity\DingDingDeal;

use ff\code\ErrorCode;
use Illuminate\Support\Facades\Date;
use models\tables\event_model\GoodsLogModel;
use models\v1_0\Goods;
use models\v1_0\GoodsLog;
use models\v1_0\GoodsSku;
use models\v1_0\GoodsVerity;
use models\v1_0\SuppliersNew;

class UpdatingListeners{

    protected $records = [
        'goods','goods_sku','goods_supply'
    ];//要记录日志的表

    protected $type = [
        'updating'
    ];///要记录的事件

    protected $verify = [
      'goods'
    ];//要记录的审核的表



    protected $pass = true;////是否拦截更新



    public function handle($event){

        $this->recordLog($event);///记录修改日志
        ///
        return $this->pass;
    }

/**
 * 记录操作日志
 * @param 
 * @param
 * @return mixed
 */
    public function recordLog($event){

        $object = $event->model->table;
        if(!in_array($object,$this->records)){
            ///没有配置 不记录
            return ;
        }

        $type = $event->type;
        if(!in_array($type,$this->type)){
            ///没有配置 不记录
            return ;
        }
        $dirty = $event->model->getDirty();

        $pr = $event->model->getKeyName();

        $object_id = $event->model->$pr;







       $record = [];///日志内容

        foreach ($dirty as $key=> $d){

            $tmp['object_field'] = $key;
            $tmp['old_value'] = $event->model->getOriginal($key);
            $tmp['new_value'] = $d;

            if(method_exists ($event->model,'updatingEvent')){
                if(!$event->model->updatingEvent($tmp)){
                    $this->pass = false;
                    $value['status'] = 1;///代表这条日志记录不真实
                    continue;
                }
            }


            $record[] = $tmp;

            ///审核逻辑  这条信息 是否触发审核逻辑 触发审核逻辑 写入一条审核日志
            $this->verify($event,$key,$tmp);
        }





        $log =[];
        $log['request_id'] =\ff::$app->router->request->requestId;
        $log['type'] =$type;
        $log['object'] =$object;
        $log['object_class'] = get_class($event->model);
        $log['object_id'] =$object_id;
        $log['content'] = json_encode($record);

        $log['operator_id'] =\ff::$app->router->request->user_id??1;
        $log['operator_name'] =\ff::$app->router->request->user_name??'admin';
        if(!$this->pass){
            $log['status'] = 1;
        }



        GoodsLogModel::create($log);//写日志


        return ;

    }

    /**
     * 处理审核逻辑
     * @param 
     * @param $key 字段名称
     * @param
     * @param
     * @return mixed
     */
    
    public function verify($event,$key,$value){

         //如果这个模型被设置为不能通过更新事件 那么必须要在审核表写入数据
        if($event->model->isPass === false){
            ////说明这个日志需要审核
            $this->pass = false;

        }

        $object = $event->model->table;
        if(!in_array($object,$this->verify)){
            return false;
        }

        $rules = $event->model->verify;

        if(!isset($rules[$key])){
            //没有制定审核规则

            return false;

        }

        if($rules[$key] == 'change'){
            ////触发审核
            $this->pass = false;//终止保存
            //\ff::$app->container->make('events')->dispatch(new Verify($goods_log));
        }elseif($rules[$key] == '>'&&($value['old_value']<$value['new_value'])){
            ////触发审核
            $this->pass = false;

        }else{
            //get_class($event->model)::where($event->model->getKeyName(),$goods_log->object_id)->update([$key=>$goods_log->new_value]);
            return false;
        }

    }

    public function formatValue($model,$key,$value){

        $key = $this->convertUnderline($key);
         $f = 'get'.$key.'Attribute';
        if( method_exists ($model,$f)){
            return $model->$f($value);
        }
        return $value;
    }

    public function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }




}