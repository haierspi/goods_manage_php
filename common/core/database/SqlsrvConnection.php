<?php

namespace ff\database;

use ff\base\Component;

class SqlsrvConnection extends Component
{
    public static $config = array();
    public static $link = array();
    public static $linkTime = array();
    public static $defaultKey = ''; //默认连接KEY
    public static $curLink;
    public static $curLinkLastSql;
    public static $curLinkTime = 0;
    public static $curKey = ''; //当前连接KEY
    public static $dbConnectRetryCount = 3;
    public static $dbConnectCount = 0;

    public function __construct()
    {
        if (func_num_args() > 0) {
            $config = func_get_arg(0);
            self::$defaultKey = $config['default'];
            self::$config = $config;
        }
    }

    public function setDbConnectRetryCount($dbConnectRetryCount)
    {
        self::$dbConnectRetryCount = $dbConnectRetryCount;
    }

    public function connect($confkey = '')
    {
        $confkey = $confkey ? $confkey : self::$defaultKey;

        $this->dbConnect($confkey);

        self::$curLink = self::$link[$confkey];
        self::$curLinkTime = self::$linkTime[$confkey];
        self::$curKey = $confkey;
        return self::$curLink;
    }

    private function dbConnect($confkey)
    {
        $config = self::$config[$confkey];
        // do {
        //     try {
        //         $link = new \PDO("sqlsrv:server={$config['host']},{$config['port']};Database={$config['database']};LoginTimeout=5;ConnectRetryCount=3", $config['username'], $config['password']);
        //         $link->setAttribute(\PDO::SQLSRV_ATTR_DIRECT_QUERY, \PDO::SQLSRV_ENCODING_UTF8);
        //     } catch (\PDOException $e) {
        //         self::$dbConnectCount ++;
        //         if(self::$dbConnectCount >= self::$dbConnectRetryCount  ){
        //             throw new SqlsrvConnectionException("SQL Server Connection Failed");
        //         }
        //     }
        // } while (self::$dbConnectCount <= self::$dbConnectRetryCount && !$link);

        try {
            $link = new \PDO("sqlsrv:server={$config['host']},{$config['port']};Database={$config['database']};LoginTimeout=5;ConnectRetryCount=3;ConnectRetryInterval=1", $config['username'], $config['password']);
            $link->setAttribute(\PDO::SQLSRV_ATTR_DIRECT_QUERY, \PDO::SQLSRV_ENCODING_UTF8);
        } catch (\PDOException $e) {
            throw new SqlsrvConnectionException($e->getMessage());
        }

        self::$link[$confkey] = $link;
        self::$linkTime[$confkey] = time();

    }

    public function checkConnect()
    {
        if (time() - self::$curLinkTime > 500) {
            $this->connect();
        }
    }

    public function execStoredProcedure($sql)
    {
        $data = [];
        $result = $this->query($sql);
        if (extension_loaded('pdo_sqlsrv')) {
            for ($i = 0; $i < 30; $i++) {
                $result->nextRowset();
                if ($result->columnCount()) {
                    break;
                }
            }
        }

        //取得所有的表名
        while ($row = $this->fetch($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function execStoredProcedureFirst($sql)
    {
        $data = [];
        $result = $this->query($sql);
        if (extension_loaded('pdo_sqlsrv')) {
            for ($i = 0; $i < 30; $i++) {
                $result->nextRowset();
                if ($result->columnCount()) {
                    break;
                }
            }
        }
        return $this->fetchColumn(0);
        //取得所有的表名
        // return $this->fetch($result);
    }

    public function execStoredProcedureFirstColumn($sql, $column_number = 0)
    {
        $data = [];
        $result = $this->query($sql);
        if (extension_loaded('pdo_sqlsrv')) {
            for ($i = 0; $i < 30; $i++) {
                $result->nextRowset();
                if ($result->columnCount()) {
                    break;
                }
            }
        }

        //取得所有的表名
        return $this->fetchColumn($result, $column_number);
    }

    public function query($sql)
    {
        $this->checkConnect();
        try {
            $queryResource = self::$curLink->query($sql);
            if ($queryResource === false) {
                $queryResource = self::$curLink->query("select @@error as error");

                $errorId = $this->fetchColumn($queryResource, 0);

                $queryResource = self::$curLink->query("SELECT text FROM sys.messages WHERE  language_id = 2052 AND  message_id = " . $errorId);
                $errorText = $this->fetchColumn($queryResource, 0);

                throw new SqlsrvQueryException($errorText);
            }
        } catch (\PDOException $e) {
            throw new SqlsrvQueryException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$sql}");
        }

        self::$curLinkLastSql = $sql;
        return $queryResource;
    }

    public function sqlQuery($sql)
    {
        $this->checkConnect();

        $queryResource = $this->query($sql);
        $result = $queryResource ? $this->fetch($queryResource) : null;

        return $result;
    }

    public function fetchAll($queryRes)
    {
        try {
            $result = $queryRes->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException | \Exception | \Error $e) {
            $lastSql = self::$curLinkLastSql;
            throw new SqlsrvResourceException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$lastSql}");
        }
        return $result;
    }

    public function fetchColumn($queryRes, $column_number = 0)
    {
        try {
            $result = $queryRes->fetchColumn($column_number);
        } catch (\PDOException | \Exception | \Error $e) {
            $lastSql = self::$curLinkLastSql;
            throw new SqlsrvResourceException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$lastSql}");
        }
        return $result;
    }

    public function fetchColumnBySql($sql, $column_number = 0)
    {
        $queryRes = $this->query($sql);
        try {
            $result = $queryRes->fetchColumn($column_number);
        } catch (\PDOException | \Exception | \Error $e) {
            $lastSql = self::$curLinkLastSql;
            throw new SqlsrvResourceException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$lastSql}");
        }
        return $result;
    }

    public function fetchBySql($sql)
    {
        $queryRes = $this->query($sql);
        try {
            $result = $queryRes->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException | \Exception | \Error $e) {
            $lastSql = self::$curLinkLastSql;
            throw new SqlsrvResourceException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$lastSql}");
        }
        return $result;
    }

    public function fetch($queryRes)
    {
        try {
            $result = $queryRes->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException | \Exception | \Error $e) {
            $lastSql = self::$curLinkLastSql;
            throw new SqlsrvResourceException($e->getMessage() . "\nSQL Server Error: \nCode {$e->getCode()}\nSQL: {$lastSql}");
        }
        return $result;
    }

    public function beginTransaction()
    {
        return self::$curLink->beginTransaction();
    }

    public function rollback()
    {
        return self::$curLink->rollback();
    }

    public function commit()
    {
        return self::$curLink->commit();
    }

    public function convert($string)
    {
        return $string;
    }

    public function insert($table, $data)
    {

        $sql = $this->implode($data);
        $cmd = 'INSERT INTO';
        return $this->query("$cmd $table $sql");
    }

    public function update($table, $data, $condition)
    {
        $sql = $this->updateImplode($data);
        if (empty($sql)) {
            return false;
        }
        $cmd = "UPDATE ";
        $res = $this->query("$cmd $table SET $sql WHERE $condition");
        return $res;
    }

    public function updateImplode($array, $glue = ',')
    {
        $fileds = [];
        foreach ($array as $k => $v) {

            if (is_null($v)) {
                $value = ' NULL';
            } elseif (is_int($v) || is_float($v)) {
                $value = $v;
            } elseif (is_string($v)) {
                if (substr($v, 0, 1) == '@') {
                    $value = $v;
                } else {
                    $value = self::$curLink->quote($v);
                }
            }
            $fileds[] = "{$k} = {$value}";
        }

        return join(',', $fileds);
    }

    public function implode($array, $glue = ',')
    {
        $filedsSql = $valuesSql = '';
        $fileds = $values = [];
        foreach ($array as $k => $v) {
            $fileds[] = $k;
            if (is_null($v)) {
                $value = ' NULL';
            } elseif (is_int($v) || is_float($v)) {
                $value = $v;
            } elseif (is_string($v)) {
                if (substr($v, 0, 1) == '@') {
                    $value = $v;
                } else {
                    $value = self::$curLink->quote($v);
                }

            }
            $values[] = $value;
        }
        $filedsSql = join(', ', $fileds);
        $valuesSql = join(', ', $values);

        return "({$filedsSql}) VALUES ({$valuesSql})";
    }

    //sqlserver 转义
    public function quote($string)
    {
        // //初始化连接
        // if (!self::$connected) {
        //     $this->connect();
        // }
        // if (extension_loaded('pdo_sqlsrv')) {

        //     if (!$string) {

        //         $string = self::$curLink->quote($string);

        //         var_dump(self::$curLink->quote($string));
        //         exit;
        //     }

        // }

        $search = array("'");
        $replace = array("''");

        return str_replace($search, $replace, $string);
        // dd($string);
        // return $string;
    }
}
