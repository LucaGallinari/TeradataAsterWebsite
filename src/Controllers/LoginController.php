<?php
/**
 * LoginController.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace Controller;

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

        return $app['twig']->render('login.html', array (
            'error'         => $app['security.last_error']($req),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }
} 