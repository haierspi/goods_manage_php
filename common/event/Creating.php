<?php
namespace common\event;

class Creating{
    public $model;

    public $type = 'creating';
    public function __construct($model)
    {
        $this->model = $model;
    }
}