<?php
namespace models\tables;

use ff\database\Model;

class GoodsModel extends Model
{

    public $table = 'goods';
    protected $primaryKey = 'goods_id';


    public function supplier()
    {
        return $this->hasOne(SupplierModel::class, 'supplier_id', 'supplier_id');
    }


}
