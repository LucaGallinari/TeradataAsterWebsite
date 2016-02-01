<?php
/**
 * TweetProvider.php
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

use App\Model\Tweet;
use ODBC_Aster\Repository\DBRecommendationRepository;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class TweetProvider
 * @package App\Repository
 */
class TweetProvider
{
    const ENTITY_NAME = 'tweets';

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
     * Loads all tweets of an event.
     *
     * @param $event_id mixed ID of the event
     *
     * @return array
     */
    public function getTweetsByEvent ($event_id)
    {
        $tweets = $this->db->getByEqualConditions(self::ENTITY_NAME, array('event_id' => $event_id));
        if ($tweets === false) {
            return false;
        }

        if (count($tweets) == 0) {
            return array();
        }

        // Retrieve all tweets as Event Object
        $tmp = new \ReflectionClass('App\\Model\\Tweet');
        foreach ($tweets as $key => $tweet) {
            try {
                $tweets[$key] = $tmp->newInstanceArgs($tweet);
            } catch(InvalidArgumentException $e) {
                $tweets[$key] = null;
            }
        }

        return $tweets;
    }

    /**
     * Insert a new Tweet
     *
     * @param Tweet $tweet
     *
     * @return bool
     */
    public function addTweet (Tweet $tweet)
    {
        if (is_null($this->db)) {
            return false;
        }

        return $this->db->insertObj(self::ENTITY_NAME, $tweet);
    }

    /**
     * Delete a Tweet
     *
     * @param mixed $tweet_id
     *
     * @return bool
     */
    public function delEvent ($tweet_id)
    {
        if (is_null($this->db)) {
            return false;
        }

        return $this->db->deleteObj(self::ENTITY_NAME, array('tweet_id' => $tweet_id));
    }

}