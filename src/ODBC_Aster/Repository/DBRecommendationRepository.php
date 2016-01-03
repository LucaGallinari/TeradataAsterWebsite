<?php
/**
 * DBRecommendationRepository.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace ODBC_Aster\Repository;

use ODBC_Aster\ODBCAsterConnectionManager;
use Silex\Application;
use App\Model\User;

/**
 * Class DBRecommendationRepository
 * The extension of ODBCAsterConnectionManager is jsut a facilitation and shouldn't
 * be done. It should be an external dependency but in this case we have a single database
 * so it's not important.
 * @package Repository\ODBC_Aster
 */
class DBRecommendationRepository extends ODBCAsterConnectionManager
{

    /**
     * @param $connectionConfigs array of strings
    */
    public function __construct ($connectionConfigs) {
        parent::__construct($connectionConfigs);
    }

    /**
     * @param $entity string name of the entity
     *
     * @return array|false users array of array or False if argument not a string
     */
    public function getAll ($entity)
    {
        if (!is_string($entity) || is_null($entity)) {
            return false;
        }
        return $this->executeQueryAndFetch("SELECT * FROM $entity;");
    }

    /**
     * Get all records of a given entity limited to a given number
     * of elements from a given offset and ordered by a given value.
     *
     * @param $entity string name of the entity
     * @param $num int number of elements to retrieve
     * @param $offset int offset
     * @param $orderby string name of the column to sort the list
     *
     * @return array|false users array of array or False if argument not a string
     */
    public function getLimitedAndOrdered ($entity, $num, $offset, $orderby)
    {
        if (!is_string($entity) || !is_numeric($num) || !is_numeric($offset)) {
            return false;
        }
        return $this->executeQueryAndFetch("SELECT * FROM $entity ORDER BY $orderby ASC LIMIT $num OFFSET $offset;");
    }

    /**
     * Get all records of a given entity based on given array of conditions.
     *
     * @param $entity string
     * @param $conditions array with key (column name), value (equal condition)
     *
     * @return array|false array of columns or FALSE
     */
    public function getByEqualConditions ($entity, $conditions = array())
    {
        if (!is_string($entity)) {
            return false;
        }

        $condsString = '';
        foreach ($conditions as $key => $cond) {
            $condsString .= "$key='$cond' AND ";
        }
        $condsString = substr($condsString, 0, -5);

        $results = $this->executeQueryAndFetch("SELECT * FROM $entity WHERE $condsString;");

        if ($results === false || is_null($results)) {
            return false;
        }
        return $results;
    }

    /**
     * Get all records of a given entity based on given array of conditions, limited to a given number
     * of elements from a given offset and ordered by a given value.
     *
     * @param $select string
     * @param $from string
     * @param $where array with key (column name), value (equal condition)
     * @param $num int number of elements to retrieve
     * @param $offset int offset
     * @param $orderby string name of the column to sort the list
     *
     * @return array|false array of columns or FALSE
     */
    public function getByEqCondsAndLimitedAndOrdered ($select, $from, $where = array(), $num, $offset, $orderby)
    {
        if (!is_string($select) || !is_string($from) || !is_numeric($num) || !is_numeric($offset)) {
            return false;
        }

        $condsString = '';
        foreach ($where as $key => $cond) {
            $condsString .= "$key='$cond' AND ";
        }
        $condsString = substr($condsString, 0, -5);

        $results = $this->executeQueryAndFetch("
            SELECT $select
            FROM $from
            WHERE $condsString
            ORDER BY $orderby ASC
            LIMIT $num
            OFFSET $offset;
        ");
        // $results = $this->executeQueryAndFetch("SELECT * FROM $entity WHERE $condsString ORDER BY $orderby ASC LIMIT $num OFFSET $offset;");
        if ($results === false || is_null($results)) {
            return false;
        }
        return $results;
    }

    /**
     * @param string $entity
     * @param mixed $obj
     *
     * @return bool success or not
     */
    public function insertObj ($entity, $obj)
    {
        $str = $obj->__toDBInsertString();
        $result = $this->executeQuery("INSERT INTO $entity VALUES (".$str.")");

        if ($result === false) {
            return false;
        }
        return true;
    }

    /**
     * @param string $entity
     * @param array $conds array of conditions 'column'=>value
     *
     * @return bool success or not
     */
    public function deleteObj ($entity, $conds = array())
    {
        $condsString = '';
        foreach ($conds as $key => $cond) {
            $condsString .= "$key='$cond' AND ";
        }
        $condsString = substr($condsString, 0, -5);

        $result = $this->executeQuery("DELETE FROM $entity WHERE $condsString;");

        if ($result === false) {
            return false;
        }
        return true;
    }

    /**
     * @param string $entity
     * @param mixed $eventId
     * @param mixed $userId
     * @param mixed $obj
     *
     * @return array|false array of columns or FALSE if error
     */
    public function updateObj ($entity, $eventId, $userId, $obj)
    {
        $str = $obj->__toDBUpdateString();
        $result = $this->executeQuery("UPDATE $entity SET ".$str." WHERE user_id='$userId' AND event_id='$eventId'");

        if ($result === false) {
            return false;
        }
        return true;
    }
}