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
     * @return array users array of array
     */
    public function getUsers ()
    {
        $results = $this->executeQueryAndFetch("SELECT * FROM users;");
        return $results;
    }

    /**
     * @param $username string
     * @return array array of columns or FALSE
     */
    public function getUserByUsername ($username)
    {
        $result = $this->executeQueryAndFetch("SELECT * FROM users WHERE email = '".$username."'");
        if ($result === false || is_null($result)) {
            return false;
        } else if (count($result)>1) {
            // TODO: there can't be more than 1 user with the same USERNAME
            echo("ERROR: There can't be more than 1 user with the same USERNAME");
        }
        return $result[0];
    }

    /**
     * @param $user User obj
     * @return array array of columns or FALSE if error
     */
    public function insertUser (User $user)
    {
        $str = $user->__toDBInsertString();
        $result = $this->executeQuery("INSERT INTO users VALUES (".$str.")");

        if ($result === false) {
            return false;
        }
        return $result[0];
    }

}