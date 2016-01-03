<?php
/**
 * EventProvider.php
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

use App\Model\Event;
use ODBC_Aster\Repository\DBRecommendationRepository;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class EventProvider
 * @package App\Repository
 */
class EventProvider
{
    const ENTITY_NAME = 'events';

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
     * Loads all the events from the DB. It is limited to a number of results
     * otherwise the web server will fail.
     *
     * @param $num int number of elements to retrieve
     * @param $offset int offset
     *
     * @return array of Event|null
     */
    public function getEvents ($num = 20, $offset = 0)
    {
        $events = $this->db->getLimitedAndOrdered(self::ENTITY_NAME, $num, $offset, 'event_id');
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

    /**
     * Loads all the events of a given user.
     *
     * @param $user_id mixed ID of the user
     *
     * @return array of Event|null or empty array
     */
    public function getEventsByUser ($user_id)
    {
        $events = $this->db->getByEqualConditions(self::ENTITY_NAME, array('user_id' => $user_id));
        if ($events === false) {
            return false;
        }

        if (count($events) == 0) {
            return array();
        }

        // Retrieve all events as Event Object
        $tmp = new \ReflectionClass('App\\Model\\Event');
        foreach ($events as $key => $event) {
            try {
                $events[$key] = $tmp->newInstanceArgs($event);
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }


    /**
     * Loads all the events from the DB. It is limited to a number of results
     * otherwise the web server will fail.
     *
     * @param $user_id int
     * @param $num int number of elements to retrieve
     * @param $offset int offset
     *
     * @return array of couple of obj with 0=Event, 1=int
     */
    public function getEventsWithInterests ($user_id, $num = 20, $offset = 0)
    {
        $query = "
        SELECT  e.*, ui.interested, ui.not_interested
        FROM    ".self::ENTITY_NAME." e LEFT JOIN user_interest ui ON (e.event_id = ui.event_id)
        WHERE   ui.user_id = '$user_id' OR ui.user_id IS NULL
        ORDER BY e.event_id ASC LIMIT $num OFFSET $offset;";

        $events = $this->db->executeQueryAndFetch($query);
        if ($events === false) {
            return false;
        }

        // Retrieve all events as Event Object and set the interest field
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
                $events[$key][0] = $tmp->newInstanceArgs($events[$key]);
                $events[$key][1] = $intval;
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }

    /**
     * Insert a new Event
     *
     * @param Event $event
     *
     * @return bool
     */
    public function addEvent (Event $event)
    {
        if (is_null($this->db)) {
            return false;
        }

        return $this->db->insertObj(self::ENTITY_NAME, $event);
    }

    /**
     * Delete an Event
     *
     * @param mixed $event_id
     * @param mixed $user_id
     *
     * @return bool
     */
    public function delEvent ($event_id, $user_id)
    {
        if (is_null($this->db)) {
            return false;
        }

        return $this->db->deleteObj(self::ENTITY_NAME, array('event_id' => $event_id, 'user_id' => $user_id));
    }

}