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