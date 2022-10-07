<?php
namespace ff\database;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;

class Manager extends \Illuminate\Database\Capsule\Manager{
    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $this->manager = $this->container->make('db');
    }
}