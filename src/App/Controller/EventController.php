<?php
/**
 * EventController.php
 *
 * PHP version 5.6
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Controller;

use App\Model\Event;
use App\Model\Tweet;
use App\Model\User;
use App\Repository\EventProvider;
use App\Repository\TweetProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twitter\TwitterManager;

class EventController {

    const NUM_EVENTS_TO_RETRIEVE = 20;

    /**
     * @param Application $app
     * @param Request $req
     *
     * @return string
     */
    public function indexAction(Application $app, Request $req)
    {
        $errors = array();
        $myEvents = array();
        $message = '';

        // added an event?
        $ok = $req->get('add_was_ok', '');
        if ($ok !== '') {
            if ($ok === true) {
                $message = 'Event added successfully!';
            } else {
                $errors[] = $req->get('error', 'Undefined error');
            }
        }

        // removed an event?
        $ok = $req->get('del_was_ok', '');
        if ($ok !== '') {
            if ($ok === true) {
                $message = 'Event removed successfully!';
            } else {
                $errors[] = $req->get('error', 'Undefined error');
            }
        }

        /** @var $token TokenInterface */
        $token = $app['security.token_storage']->getToken();
        /** @var $user User */
        $user = $token->getUser();

        if ($app['odbc_aster'] !== false) {

            // user events
            $eventsProvider = new EventProvider($app['odbc_aster']);
            $myEvents = $eventsProvider->getEventsByUser($user->getId());
            if ($myEvents === false) {
                $errors[] = "Error while retrieving the events of the user! (Bad Query?)";
            }

        } else {
            $errors[] = "Could not connect to the DB!";
        }


        return $app['twig']->render('myevents.twig', array (
            'my_events' => $myEvents,
            'message'   => $message,
            'errors'    => $errors,
            'page'      => 'myevents',
        ));
    }


    /**
     * @param Application $app
     * @param Request $req
     *
     * @return string
     */
    public function viewAction(Application $app, Request $req)
    {
        $event = '';
        $tweets = array();

        // added an event?
        $eventid = $req->get('eventid', null);
        if (is_null($eventid)) {
            return $app->redirect($app['url_generator']->generate('myevents'));
        }

        if ($app['odbc_aster'] !== false) {

            $eventsProvider = new EventProvider($app['odbc_aster']);
            $event = $eventsProvider->getEventById($eventid);
            if ($event === false) {
                $errors[] = "Error while retrieving the event! (Bad Query?)";
            }

            if ($event !== false) {
                $tweetProvider = new TweetProvider($app['odbc_aster']);
                $tweets = $tweetProvider->getTweetsByEvent($eventid);
                if ($tweets === false) {
                    $errors[] = "Error while retrieving the event! (Bad Query?)";
                }
            }

        } else {
            $errors[] = "Could not connect to the DB!";
        }

        return $app['twig']->render('event.twig', array (
            'event'     => $event,
            'tweets'    => $tweets,
            'page'      => 'event',
        ));
    }


    /**
     * @param Request $req
     * @param Application $app
     *
     * @return bool
     */
    public function addAction(Request $req, Application $app)
    {
        $error = '';
        $ok = false;

        if ($app['odbc_aster'] !== false) {

            if (!is_null($req->request->get('action'))) {

                // get parameters
                $userid = $app['security.token_storage']->getToken()->getUser()->getId();
                $city = $req->request->get('city', '');
                $state = $req->request->get('state', '');
                $country = $req->request->get('country', '');
                $zip = $req->request->get('zip', '');
                $startt = $req->request->get('startt', '');
                $title = $req->request->get('title', '');
                $descr = $req->request->get('description', '');
                $hashtag = $req->request->get('hashtag', '');

                //checks
                if ($startt != '') {

                    /**@var $eventProvider EventProvider */
                    $eventProvider = new EventProvider($app['odbc_aster']);
                    $eventid = uniqid(rand().'_');
                    $event = new Event($eventid, $userid, $startt, $city, $state, $zip, $country, $title, $descr);
                    /*if ($eventProvider->addEvent($event)) {
                        $ok = true;
                    } else {
                        $error = 'The event is already present. Please change some values';
                    }*/


                    if ($hashtag != '') {

                        /**@var $tweetProvider TweetProvider */
                        $tweetProvider = new TweetProvider($app['odbc_aster']);

                        // get tweets with an API call
                        $twitterManager = new TwitterManager($app['twitter.config']);
                        $tweets = $twitterManager->search($hashtag);

                        if (count($tweets) != 0) {
                            $count = 0;
                            // for each tweet
                            foreach ($tweets as $tweet) {
                                if ($count == 10) {
                                    break;
                                }
                                $id = $tweet["ID"];
                                $text = $tweet["TEXT"];
                                $date = $tweet["CREATED_AT"];
                                $userName = $tweet["USER_NAME"];
                                $userImage = $tweet["IMAGE_PROFILE"];
                                $url = $tweet["URL"];
                                $rc = $tweet["RTWEET_COUNT"];
                                $lang = $tweet["LANG"];

                                $tweet = new Tweet($id, $eventid, $text, $userName, $userImage, $url, $date, $lang, $rc);
                                if (!$tweetProvider->addTweet($tweet)) {
                                    $error = 'Cannot add the tweet. Continue.';
                                };
                                ++$count;
                            }
                        } else {
                            $error = 'No tweet found for the given hashtag: '.$hashtag;
                        }
                    }
                } else {
                    $error = 'Invalid start time.';
                }
            }
        } else {
            $error = 'Could not connect to the database! Please retry soon.';
        }

        // forward to 'myevents'
        $subRequest = Request::create($app['url_generator']->generate('myevents'), 'GET', array('add_was_ok' => $ok, 'error' => $error));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * @param Request $req
     * @param Application $app
     *
     * @return bool
     */
    public function delAction(Request $req, Application $app)
    {
        $error = '';
        $ok = false;

        if ($app['odbc_aster'] !== false) {

            // get parameters
            $userid = $app['security.token_storage']->getToken()->getUser()->getId();
            $eventid = $req->get('eventid', '');

            //checks
            if ($eventid != '') {

                /**@var $eventProvider EventProvider */
                $eventProvider = new EventProvider($app['odbc_aster']);

                if ($eventProvider->delEvent($eventid, $userid)) {
                    $ok = true;
                } else {
                    $error = 'Cannot find the event to delete!';
                }

            } else {
                $error = 'Invalid event ID.';
            }

        } else {
            $error = 'Could not connect to the database! Please retry soon.';
        }

        // forward to 'myevents'
        $subRequest = Request::create($app['url_generator']->generate('myevents'), 'GET', array('del_was_ok' => $ok, 'error' => $error));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }


} 