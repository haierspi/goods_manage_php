<?php
namespace models\tables;

use ff\database\Model;

class OrderModel extends Model
{

    public $table = 'order';
    protected $primaryKey = 'order_id';

    public $hannels = null;


    public function goods()
    {
        return $this->hasOne(GoodsModel::class, 'goods_id', 'goods_id');
    }

    public function supplier()
    {
        return $this->hasOne(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

}
