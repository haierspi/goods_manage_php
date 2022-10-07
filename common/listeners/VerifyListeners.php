<?php
namespace common\listeners;

use models\v1_0\Goods;
use models\v1_0\GoodsVerity;

/**
 * 审核事件监听者
 * @param
 * @return mixed
 */
class VerifyListeners{

    public function handle($event){
/////调用审核接口
       //修改审核状态

     // $a =  GoodsVerity::where('log_id',15)->update(['status'=>1]);


        //修改表数据

      // $res = Goods::where('goods_id',196429)->update(['in_price'=>'250','supp_url'=>'dwd','suppliers_id'=>'123']);
  //  dd($res);
    }


}