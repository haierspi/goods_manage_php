<?php
namespace ff\auth;

use ff;
use common\ErrorCode;
use ff\base\Controller;
use ff\network\Request;
use ff\base\Application;
use ff\helpers\StringLib;
use models\tables\SupplierUser;

/**
 * 供应商签名控制器
 */

class SupplierSignAuthController extends Controller
{
    public function beforeAction()
    {
        $verifyResult = $this->auth();
        if (!is_null($verifyResult)) {
            return $verifyResult;
        }
        parent::beforeAction();
    }

    public function auth()
    {
        $vars = $this->request->vars;

        $supplierUid = $vars['supplierUid'];

        $supplierUser = SupplierUser::where('supplier_manage_uid', $supplierUid)
            ->select(
                'supplier_manage_uid',
                'name',
                'supplier_ids',
                'updated_at',
                'created_at',
                'sign_auth_key'
            )->first();

        if (!$supplierUser) {
            return ErrorCode::SUPPLIER_USER_NOT_EXIST();
        }

        $signkey = $supplierUser->sign_auth_key;

        if ($signkey) {
            if (!isset($vars['sign']) || $vars['sign'] != StringLib::getArySign($vars, $signkey)) {
                return ErrorCode::SIGN_FAILED();
            }
        }

        if (isset($supplierUser->supplier_ids)) {
            $supplierUser->supplier_ids = array_filter(explode(',', $supplierUser->supplier_ids));
        } else {
            $supplierUser->supplier_ids = [];
        }

        $this->user = $supplierUser;


        return null;

    }
}
