<?php
namespace ff\database;

use ff;
use models\tables\AdminUserModel;

class AdminUserAuthModel
{
    public $inited = false;
    public $attributes = [];
    public $instance = null;

    public function __construct($isInstance = true)
    {
        if ($isInstance) {
            $this->instance = ff::createObject(__CLASS__, [false]);
        }
    }

    public function init($auid = null, $token = null)
    {

        if (is_null($auid) || is_null($token)) {
            return null;
        }

        if (AdminUserModel::find($auid)->doesntExist()) {
            return null;
        }

        $attributes = AdminUserModel::find($auid)->getAttributes();


        $this->instance->attributes = $attributes;
        $this->instance->inited = true;

        return $attributes;

    }

    public function asArray()
    {
        return $this->instance->attributes;
    }

    public function __get($name)
    {
        $name = ff\helpers\StringLib::uncamelize($name);
        return $this->instance->attributes[$name] ?? null;
    }

    public function __isset($name)
    {
        $name = ff\helpers\StringLib::uncamelize($name);
        return isset($this->instance->attributes[$name]);
    }

}
