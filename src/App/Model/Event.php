<?php
/**
 * Event.php
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
 * Class Event
 * @package App\Model
 */
class Event
{

    protected $event_id;
    protected $user_id;
    protected $start_time;
    protected $city;
    protected $state;
    protected $zip;
    protected $country;
    // protected $c_arr;

    public function __construct(
        $event_id,
        $user_id,
        $start_time = '',
        $city = '',
        $state = '',
        $zip = '',
        $country = ''
    ) {
        if ($event_id == '' || is_null($event_id)) {
            throw new \InvalidArgumentException('EventID is not valid.');
        } else if ($user_id == '' || is_null($user_id)) {
            throw new \InvalidArgumentException('UserID is not valid.');
        }

        $this->event_id     = $event_id;
        $this->user_id      = $user_id;
        $this->start_time   = $start_time;
        $this->city         = $city;
        $this->state        = $state;
        $this->zip          = $zip;
        $this->country      = $country;
        // $this->c_arr        = $c_arr;

    }

    public function __toString()
    {
        return $this->getEventId();
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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
     * @param string $start_time
     */
    public function setStartTime($start_time)
    {
        $this->start_time = $start_time;
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
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

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }



}

