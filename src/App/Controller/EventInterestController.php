<?php
/**
 * EventInterestController.php
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

use App\Model\User;
use App\Repository\EventProvider;
use App\Repository\UserInterestProvider;
use App\Repository\UserRecommendationProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EventInterestController {

    /**
     * @param Application $app
     * @param Request $req
     *
     * @return Response
     */
    public function addAction(Application $app, Request $req)
    {
        $type = $req->get('type', null);
        $eventId = $req->get('id', null);

        if (is_null($type) || is_null($eventId) || !is_numeric($type) || !is_numeric($eventId)) {
            return new Response('Wrong parameter', 404);
        }
        if ($app['odbc_aster'] === false) {
            return new Response('Could not connect to the DB', 404);
        }

        $interested = 0;
        $not_interested = 0;
        switch ($type) {
            case 0:// 0,1
                $not_interested = 1;
                break;
            case 1:// 0,0 is ok
                break;
            case 2:// 1,0
                $interested = 1;
                break;
            default:
                return new Response('Not valid type', 404);
                break;
        }

        /** @var $user User */
        $user = $app['security.token_storage']->getToken()->getUser();
        $userId = $user->getId();

        /**@var $userIntProvider UserInterestProvider */
        $userIntProvider = new UserInterestProvider($app['odbc_aster']);

        if ($userIntProvider->addUserInterest($userId, $eventId, $interested, $not_interested)) { // try to insert
            return new Response('Ok', 200);
        } else if ($userIntProvider->updateUserInterest($userId, $eventId, $interested, $not_interested)) { // try to update
            return new Response('Ok', 200);
        } else {
            return new Response('Could not add the event to interest', 404);
        }

    }



} 