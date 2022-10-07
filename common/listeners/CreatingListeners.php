<?php
namespace common\listeners;

use Illuminate\Support\Facades\Date;
use models\tables\event_model\GoodsLogModel;
use models\v1_0\GoodsLog;
use models\v1_0\GoodsVerity;

class CreatingListeners{
    protected $pass = true;////是否拦截更新
    public $type = 'creating';
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



        $attributes = $event->model->getAttributes();

        $pr = $event->model->getKeyName();

        $object_id = $event->model->$pr;
        $type = $event->type;




        foreach ($attributes as $key=> $d){

            $tmp['object_field'] = $key;
            $tmp['old_value'] = '';
            $tmp['new_value'] = $d;

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

    public function verify($event){

        //如果这个模型被设置为不能通过更新事件 那么必须要在审核表写入数据
        if($event->model->isPass === false){
            ////说明这个日志需要审核
            $this->pass = false;
        }

        return true;

    }
}