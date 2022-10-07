<?php
namespace common\event;

class Updating{
    public $model;

    public $type = 'updating';
    public function __construct($model)
    {
     $this->model = $model;
    }




}