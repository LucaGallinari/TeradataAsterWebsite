<?php
/**
 * Tweet.php
 *
 * PHP version 5.6
 *
 * @category App
 * @package  TeradataAsterWebsite
 * @author   Francesco Bartolacelli, Luca Gallinari
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Model;

/**
 * Class Tweet
 * @package App\Model
 */
class Tweet
{
    protected $tweet_id;
    protected $event_id;
    protected $content;
    protected $userName;
    protected $userImage;
    protected $url;
    protected $date;
    protected $lang;
    protected $rc;

    public function __construct(
        $tweet_id,
        $event_id,
        $content,
        $date = '',
        $userName,
        $userImage = '',
        $url = '',
        $rc = 0,
        $lang = ''
    ) {
        if ($tweet_id == '' || is_null($tweet_id)) {
            throw new \InvalidArgumentException('TweetID is not valid.');
        }
        if ($event_id == '' || is_null($event_id)) {
            throw new \InvalidArgumentException('EventID is not valid.');
        }
        if ($content == '' || is_null($content)) {
            throw new \InvalidArgumentException('Text is not valid.');
        }
        if ($userName == '' || is_null($userName)) {
            throw new \InvalidArgumentException('Username is not valid.');
        }

        $this->tweet_id     = $tweet_id;
        $this->event_id     = $event_id;
        $this->content      = $content;
        $this->userName     = $userName;
        $this->userImage    = $userImage;
        $this->url          = $url;
        $this->date         = $date;
        $this->lang         = $lang;
        $this->rc           = $rc;
    }

    public function __toString()
    {
        return $this->getTweetID();
    }

    public function __toDBInsertString()
    {
        return sprintf(
            "'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s'",
            $this->tweet_id,
            $this->event_id,
            $this->content,
            $this->date,
            $this->userName,
            $this->userImage,
            $this->url,
            $this->rc,
            $this->lang
        );
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
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
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $text
     */
    public function setContent($text)
    {
        $this->content = $text;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $tweet_id
     */
    public function setTweetId($tweet_id)
    {
        $this->tweet_id = $tweet_id;
    }

    /**
     * @return mixed
     */
    public function getTweetId()
    {
        return $this->tweet_id;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $userImage
     */
    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;
    }

    /**
     * @return string
     */
    public function getUserImage()
    {
        return $this->userImage;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param int $rc
     */
    public function setRc($rc)
    {
        $this->rc = $rc;
    }

    /**
     * @return int
     */
    public function getRc()
    {
        return $this->rc;
    }

}

