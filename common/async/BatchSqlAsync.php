<?php
namespace common\async;

use ff;
use common\dingding\DingDingLogin;
use common\dingding\DingDingApproval;
use common\dingding\DingDingApprovalServer;

Class BatchSqlAsync
{
    private $approvalData = [];

    private $approvalTypeList = [
        1 => 'lwPriceChangeFirst',
        2 => 'lwPriceChangeSecond',
        3 => 'lwPriceChangeThird',
        4 => 'popPriceChangeFirst',
        5 => 'popPriceChangeSecond',
        6 => 'popPriceChangeThird',
        7 => 'lwSupplierChange',
        8 => 'popSupplierChange',
        9 => 'lwSupplierChangeFirst',
        10 => 'lwSupplierChangeSecond',
        11 => 'lwSupplierChangeThird',
        12 => 'popSupplierChangeFirst',
        13 => 'popSupplierChangeSecond',
        14 => 'popSupplierChangeThird',
    ];


}