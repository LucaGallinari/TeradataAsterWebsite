<?php
/**
 * UserInterestProvider.php
 *
 * PHP version 5.6
 *
 * @category App
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Repository;

use ODBC_Aster\Repository\DBRecommendationRepository;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class UserInterestProvider
 * @package App\Repository
 */
class UserInterestProvider
{
    const ENTITY_NAME = 'user_interest';

    /** @var DBRecommendationRepository */
    private $db;

    /**
     * @param DBRecommendationRepository $db
     */
    public function __construct(DBRecommendationRepository $db)
    {
        $this->db = $db;
    }

    /**
     * Loads all recommended events for a given user. It is limited to a number of results
     * otherwise the web server will fail.
     *
     * @param $user_id int
     * @param $num int number of elements to retrieve
     * @param $offset int offset
     *
     * @return array of Event|null
     */
    public function getUserInterests ($user_id, $num = 20, $offset = 0)
    {
        $events = $this->db->getByEqCondsAndLimitedAndOrdered(
            'events.*',
            self::ENTITY_NAME . " RIGHT OUTER JOIN events ON (".self::ENTITY_NAME.".event_id = events.event_id) ",
            array(self::ENTITY_NAME . '.user_id' => $user_id),
            $num, $offset, 'events.event_id'
        );
        if ($events === false) {
            return false;
        }

        // Retrieve all events as Event Object
        $tmp = new \ReflectionClass('App\\Model\\Event');

        foreach ($events as $key => $event) {
            try {
                $events[$key] = $tmp->newInstanceArgs($events[$key]);
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }

}