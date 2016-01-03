<?php
/**
 * UserInterest.php
 *
 * PHP version 5.6
 *
 * @category App
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Model;

/**
 * Class UserInterest
 * @package App\Model
 */
class UserInterest
{

    protected $user_id;
    protected $event_id;
    protected $interested;
    protected $not_interested;
    protected $request_time;
    protected $invited;

    public function __construct(
        $user_id,
        $event_id,
        $interested = 0,
        $not_interested = 0,
        $request_time = '',
        $invited = 0
    ) {
        if ($event_id == '' || is_null($event_id)) {
            throw new \InvalidArgumentException('EventID is not valid.');
        } else if ($user_id == '' || is_null($user_id)) {
            throw new \InvalidArgumentException('UserID is not valid.');
        }
        if ($request_time === '') {
            $request_time = date("Y-m-d");
        }

        $this->user_id          = $user_id;
        $this->event_id         = $event_id;
        $this->interested       = $interested;
        $this->not_interested   = $not_interested;
        $this->request_time     = $request_time;
        $this->invited          = $invited;

    }

    public function __toString()
    {
        return $this->getUserId().",".$this->getEventId();
    }

    public function __toDBInsertString()
    {
        return sprintf(
            "'%s', '%s', '%s', '%s', '%s', '%s'",
            $this->user_id,
            $this->event_id,
            $this->invited,
            $this->request_time,
            $this->interested,
            $this->not_interested
        );
    }

    public function __toDBUpdateString()
    {
        return sprintf(
            "invited='%s', request_time='%s', interested='%s', not_interested='%s'",
            $this->invited,
            $this->request_time,
            $this->interested,
            $this->not_interested
        );
    }

    /**
     * @param mixed $event_id
     */
    public function setEventId($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @param int $interested
     */
    public function setInterested($interested)
    {
        $this->interested = $interested;
    }

    /**
     * @return int
     */
    public function getInterested()
    {
        return $this->interested;
    }

    /**
     * @param int $invited
     */
    public function setInvited($invited)
    {
        $this->invited = $invited;
    }

    /**
     * @return int
     */
    public function getInvited()
    {
        return $this->invited;
    }

    /**
     * @param int $not_interested
     */
    public function setNotInterested($not_interested)
    {
        $this->not_interested = $not_interested;
    }

    /**
     * @return int
     */
    public function getNotInterested()
    {
        return $this->not_interested;
    }

    /**
     * @param bool|string $request_time
     */
    public function setRequestTime($request_time)
    {
        $this->request_time = $request_time;
    }

    /**
     * @return bool|string
     */
    public function getRequestTime()
    {
        return $this->request_time;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }


}

