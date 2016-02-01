<?php
/**
 * ODBCAsterConnectionManager.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace ODBC_Aster;

use Silex\Application;

/**
 * Class ODBCAsterConnectionManager
 * @package ODBC_Aster
 */
class ODBCAsterConnectionManager
{
    protected $driver;
    protected $host;
    protected $dbname;
    protected $user;
    protected $pass;

    /** @var resource */
    protected $connection = NULL;

    /**
     * @param $configs array
    */
    public function __construct ($configs = array()) {
        $this->driver   = $configs['driver'];
        $this->host     = $configs['host'];
        $this->dbname   = $configs['database'];
        $this->user     = $configs['username'];
        $this->pass     = $configs['password'];
    }

    /**
     * @return boolean successfully connected or not
    */
    public function connect ()
    {
        // No changes needed from now on
        $connection_string = "Driver=$this->driver;Server=$this->host;Database=$this->dbname";
        // $connection_string = "DSN=testdsn";

        $this->connection = @odbc_connect($connection_string, $this->user, $this->pass);
        return $this->connection;
    }

    /**
     * @param $query string query to execute
     * @return resource results or FALSE
    */
    public function executeQuery ($query)
    {
        if (!$this->isConnected()) {
            return false;
        }
        $ress = null;
        $ress = @odbc_exec($this->connection, $query);
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
     * @param $query string query to execute
     *
     * @return array|false of array results
     */
    public function executeQueryAndFetch ($query)
    {
        // exe query
        $results = $this->executeQuery($query);
        if ($results === false) {
            return false;
        }
        // fetch results into an array
        $out = array();
        while($res = odbc_fetch_array($results)) {
            $out[] = $res;
            if ($res === false) {
                return false;
            }
        }
        return $out;
    }

    /**
     * @return boolean connected or not
    */
    public function isConnected()
    {
        return !($this->connection === false);
    }
}