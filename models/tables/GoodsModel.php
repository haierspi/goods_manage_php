<?php
namespace models\tables;

use ff\database\Model;

class GoodsModel extends Model
{

    public $table = 'goods';
    protected $primaryKey = 'goods_id';
    public $suppliers = null;

    public function supplier()
    {
        return $this->hasOne(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function suppliers()
    {
        if (is_null($this->suppliers)) {
            $this->suppliers = SupplierModel::get();
        }
        return $this->suppliers;
    }

}
