<?php
/**
 * AppController.php
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
use App\Model\User;
use App\Repository\EventProvider;
use App\Repository\UserInterestProvider;
use App\Repository\UserRecommendationProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AppController {

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
        $eventsRec = array();
        $events = array();
        $eventsInt = array();

        /** @var $token TokenInterface */
        $token = $app['security.token_storage']->getToken();
        /** @var $user User */
        $user = $token->getUser();
        $id = $user->getId();

        $page = $req->get('page', 0);
        if (!is_numeric($page) || $page < 0) {
            $page = 0;
        }

        if ($app['odbc_aster'] !== false) {

            // recommended events
            $userRecProvider = new UserRecommendationProvider($app['odbc_aster']);
            $eventsRec = $userRecProvider->getUserRecommendations(
                $id,
                self::NUM_EVENTS_TO_RETRIEVE,
                $page * self::NUM_EVENTS_TO_RETRIEVE
            );
            if ($eventsRec === false) {
                $errors[] = "Error while retrieving recommended events for the user! (Bad Query?)";
            }

            // general events
            $eventsProvider = new EventProvider($app['odbc_aster']);
            $events = $eventsProvider->getEventsWithInterests(
                $id,
                self::NUM_EVENTS_TO_RETRIEVE,
                $page * self::NUM_EVENTS_TO_RETRIEVE
            );
            if ($events === false) {
                $errors[] = "Error while retrieving events! (Bad Query?)";
            }

            // interested events
            $userIntProvider = new UserInterestProvider($app['odbc_aster']);
            $eventsInt = $userIntProvider->getUserInterests(
                $id,
                self::NUM_EVENTS_TO_RETRIEVE,
                $page * self::NUM_EVENTS_TO_RETRIEVE
            );
            if ($eventsInt === false) {
                $errors[] = "Error while retrieving interested events for the user! (Bad Query?)";
            }

        } else {
            $errors[] = "Could not connect to the DB!";
        }

        return $app['twig']->render('app.twig', array (
            'user'      => $user,
            'rec_events'=> $eventsRec,
            'events'    => $events,
            'int_events'=> $eventsInt,
            'p'         => $page + 1,
            'error'     => $errors,
            'page'      => 'discover',
        ));
    }

    /**
     * @param Application $app
     * @return string
     */
    public function testAsterConnectionAction(Application $app)
    {
        if ($app['odbc_aster']->isConnected()) {
            return "Connection established!";
        } else{
            return "Connection could not be established.";
        }
    }
} 