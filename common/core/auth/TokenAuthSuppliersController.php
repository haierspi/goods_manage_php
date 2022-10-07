<?php
namespace ff\auth;

use common\ErrorCode;
use ff\database\UserSuppliersModel;

class TokenAuthSuppliersController extends TokenAuthController
{
    public function init($uid, $token)
    {
        //select db check user token

        $this->user = new UserSuppliersModel();
        $this->user->init($uid, $token);

        if (is_null($this->user) || !$this->user->uid) {
            return ErrorCode::TOKEN_FAILED();
        }

        if (is_null($this->user) || !$this->user->uid) {
            return ErrorCode::TOKEN_FAILED();
        }

        if (empty($this->user->supplierIds)) {
            return ErrorCode::SUPPLIER_USER_NOT_RELATED();
        }

        return null;
    }

}
