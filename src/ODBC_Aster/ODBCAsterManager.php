<?php

namespace ODBC_Aster;

use Silex\Application;

class ODBCAsterManager
{
    protected $driver;
    protected $host;
    protected $dbname;
    protected $user;
    protected $pass;

    /** @var resource */
    protected $conn = NULL;

    /**
     * @param $configs array
    */
    public function __construct ($configs = array()) {
        $this->driver   = $configs['driver'];
        $this->host     = $configs['host'];
        $this->dbname   = $configs['database'];
        $this->user     = $configs['username'];
        $this->pass     = $configs['password'];

        $this->connect();
    }

    /**
     * @return boolean successfully connected or not
    */
    public function connect ()
    {
        // No changes needed from now on
        $connection_string = "Driver=$this->driver;Server=$this->host;Database=$this->dbname";
        // $connection_string = "DSN=testdsn";
        $this->conn = odbc_connect($connection_string, $this->user, $this->pass);

        return $this->conn;
    }

    /**
     * @param $query string query to execute
     * @return resource results or FALSE
    */
    public function executeQuery ($query)
    {
        // $stmt    = odbc_prepare($this->conn, 'CALL myproc(?,?,?)');
        // $success = odbc_execute($stmt, array($a, $b, $c));
        $ress = null;
        try {
            $ress = odbc_exec($this->conn, $query);
        } catch(\Exception $e) {
            echo "Error: ".$e->getMessage();
        }
        return $ress;
    }

    /**
     * @param $results resource results of a previous query
     * @return array one record or FALSE
    */
    public function fetch ($results)
    {
        if ($results===false) {
            return false;
        }
        $record = odbc_fetch_array ($results);
        if ($record === false) {
            return false;
        }
        return $record;
    }

    /**
     * @return boolean connected or not
    */
    public function isConnected()
    {
        return !($this->conn === false);
    }
}