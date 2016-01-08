<?php
/**
 * TwitterManager.php
 *
 * PHP version 5.6
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Francesco Bartolacelli
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace Twitter;

class TwitterManager {
    /** Set access tokens here - see: https://dev.twitter.com/apps/ **/

    private $settings;

    /**
     * @param array $settings
     */
    function __construct($settings = array()) {
        $this->settings = $settings;
    }

    /**
     * @param $key_event
     *
     * @return array
     */
    function search($key_event){

        $url ="https://api.twitter.com/1.1/search/tweets.json";
        $requestMethod = "GET";

        //Creazione array
        $results = array();
        $getfield = '?q=' . $key_event . '&count=100&result_type=mixed&lang=en'; //Stringa di ricerca

        // creo collegamento con Twitter
        /**@var $twitter TwitterAPIExchange*/
        $twitter = new TwitterAPIExchange($this->settings);

        //get tweets from Twitter
        $string = json_decode(
            $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),
            $assoc = true
        );

        foreach ($string['statuses'] as $value) {
            /*
                check if array hasn't media or retweeteed and
                if no presence on Results array,
                insert tweet array
            */
            if (isset($value["entities"]["media"])) {
                if (count($value["entities"]["media"]) > 0) {
                    continue;
                }
            }

            if (isset($value['retweeted_status'])) {
                continue;
            }

            if ($this->check($value['user']['name'], $results) == true) {

                $array_url = array();
                /* insert in url_each url of instance*/
                foreach ($value['entities']['urls'] as $url) {
                    array_push($array_url, $url['expanded_url']);

                }
                /* load array of instance*/
                $tweets = array(
                    "ID" => $value['id'],
                    "TEXT" => $this->cleanText($value['text']),
                    "URL" => stripslashes(implode(';', $array_url)),
                    "CREATED_AT" => date('Y-m-d H:i:s', strtotime($value['created_at'])),
                    "USER_NAME" => stripslashes($value['user']['name']),
                    "IMAGE_PROFILE" => stripslashes($value['user']['profile_image_url_https']),
                    "RTWEET_COUNT" => $value['retweet_count'],
                    "LANG" => $value['lang']
                );
                /* load final array*/
                array_push($results, $tweets);
            }
        }
        return $results;
    }

    /**
     * function to check
     *
     * @param $check
     * @param $vector
     *
     * @return bool
     */
    function check($check, $vector){
        foreach ($vector as $item) {
            if ($item['USER_NAME'] == $check) {
                return false;
            }
        }
        return true;
    }

    private function cleanText($text) {
        $text = addslashes($text);
        /*$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $text = preg_replace($regexEmoticons,'', $text);
        $text = html_entity_decode($text);*/
        return $text;
    }

    private function cleanName($string)
    {
        $string = str_replace("'", "<apostrofo>", $string);
        return $string;
    }
}



