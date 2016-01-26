<?php
/**
 * UserRecommendationProvider.php
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
 * Class UserRecommendationProvider
 * @package App\Repository
 */
class UserRecommendationProvider
{
    const ENTITY_NAME = 'user_recommendation';

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
    public function getUserRecommendations ($user_id, $num = 20, $offset = 0)
    {
        /*
        $events = $this->db->getByEqCondsAndLimitedAndOrdered(
            'events.*',
            self::ENTITY_NAME . " RIGHT JOIN events ON (".self::ENTITY_NAME.".event_id = events.event_id) ",
            array(self::ENTITY_NAME . '.user_id' => $user_id),
            $num, $offset, 'events.event_id'
        );
        */
        $query = "
        SELECT  e.*, ui.interested, ui.not_interested
        FROM    ".self::ENTITY_NAME." ur JOIN (events e LEFT JOIN user_interest ui ON (e.event_id = ui.event_id)) ON (ur.event_id = e.event_id)
        WHERE   ur.user_id = '$user_id'
        AND     ui.user_id = '$user_id' OR ui.user_id IS NULL
        ORDER BY e.event_id
        LIMIT $num OFFSET $offset;";

        $events = $this->db->executeQueryAndFetch($query);
        if ($events === false) {
            return false;
        }

        // Retrieve all events as Event Object
        $tmp = new \ReflectionClass('App\\Model\\Event');
        foreach ($events as $key => $event) {

            // calculate interests as single value
            $int = -1;
            $not_int = -1;
            if (array_key_exists("interested", $event)) {
                $int = $event['interested'];
            }
            if (array_key_exists("not_interested",$event)) {
                $not_int = $event['not_interested'];
            }
            if ($int == -1 && $not_int == -1) {
                $intval = -1;
            } else if ($int == 0) {
                if ($not_int == 0) {
                    $intval = 1;
                } else {
                    $intval = 0;
                }
            } else {
                $intval = 2;
            }

            // create new Event obj
            try {
                $events[$key] = EventProvider::toArrayForCvalues($events[$key]);
                $events[$key][0] = $tmp->newInstanceArgs($events[$key]);
                $events[$key][1] = $intval;
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }

}