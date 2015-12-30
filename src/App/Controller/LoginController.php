<?php
/**
 * LoginController.php
 *
 * PHP version 5.5
 *
 * @category App
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class LoginController {
    /**
     * @var Application;
     */
    protected $app = null;

    /**
     * @param Request $req
     * @param Application $app
     */
    public function indexAction(Request $req, Application $app)
    {
        if ($app['odbc_aster'] === false) {
            $loginError = "Could not connect to the DB!";
        } else {
            $loginError = $app['security.last_error']($req);
        }

        return $app['twig']->render('login.twig', array (
            'loginError'    => $loginError,
            'last_username' => $app['session']->get('_security.last_username'),
            'page'          => 'login',
        ));
    }
} 