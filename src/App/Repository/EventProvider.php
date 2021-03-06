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
                $events[$key] = $this->toArrayForCvalues($events[$key]);
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
                $event = $this->toArrayForCvalues($event);
                $events[$key] = $tmp->newInstanceArgs($event);
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }

    /**
     * Loads all the events of a given user.
     *
     * @param $user_id int
     * @param $keywords string
     *
     * @return array of Event|null or empty array
     */
    public function getEventsByKeywordsWithInterest($user_id, $keywords)
    {
        $keyword_tokens = explode(' ', $keywords);

        $keywords_sql = "(e.name LIKE'%";
        $keywords_sql .= implode("%' OR e.name LIKE '%", $keyword_tokens) . "%'";
        $keywords_sql .= " OR e.description LIKE'%";
        $keywords_sql .= implode("%' OR e.description LIKE '%", $keyword_tokens) . "%'";
        $keywords_sql .= ")";

        $query = "
        SELECT  e.*, ui.interested, ui.not_interested
        FROM    ".self::ENTITY_NAME." e LEFT JOIN user_interest ui ON (e.event_id = ui.event_id)
        WHERE   (ui.user_id = '$user_id' OR ui.user_id IS NULL)
        AND     $keywords_sql;";

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
                $events[$key] = $this->toArrayForCvalues($events[$key]);
                $events[$key][0] = $tmp->newInstanceArgs($events[$key]);
                $events[$key][1] = $intval;
            } catch(InvalidArgumentException $e) {
                $events[$key] = null;
            }
        }

        return $events;
    }

    /**
     * Load an event
     *
     * @param $eventid int
     *
     * @return array
     */
    public function getEventById($eventid)
    {
        $event = $this->db->getByEqualConditions(self::ENTITY_NAME, array('event_id' => $eventid));
        if ($event === false) {
            return false;
        }

        $event = $event[0];
        $tmp = new \ReflectionClass('App\\Model\\Event');
        $event = $this->toArrayForCvalues($event);
        $event = $tmp->newInstanceArgs($event);

        return $event;
    }

    /**
     * @param $event array $event
     * @return array
     */
    final static public function toArrayForCvalues($event) {
        $cvalues = array();
        // for cn
        for ($i = 1; $i <= 100; ++$i) {
            $cvalues[] = $event['c'.$i];
            unset($event['c'.$i]);
        }
        // cother
        $cvalues[] = $event['cother'];
        unset($event['cother']);

        $event[] = $cvalues;
        return $event;
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
    public function getEventsWithInterests ($user_id, $num = 10, $offset = 0)
    {
        $random = lcg_value();
        $query = "
        SELECT  e.*, ui.interested, ui.not_interested
        FROM    ".self::ENTITY_NAME." e LEFT JOIN user_interest ui ON (e.event_id = ui.event_id)
        WHERE   ui.user_id = '$user_id' OR ui.user_id IS NULL
        AND     CAST(e.event_id as BIGINT) >= ".$random." * (SELECT CAST(MAX(event_id) as BIGINT) FROM events)
        LIMIT $num OFFSET $offset;";

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
                $events[$key] = $this->toArrayForCvalues($events[$key]);
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