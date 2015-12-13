<?php
/**
 * AdminController.php
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
use Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdminController {

    /*protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }*/

    /**
     * @param Application $app
     * @return string
     */
    public function indexAction(Application $app)
    {
        /** @var $token TokenInterface */
        $token = $app['security.token_storage']->getToken();

        if (null !== $token) {
            $name = $token->getUser()->getId();
            /* @var $user User

            $user = $token->getUser();
            $name = $user->getUsername();*/
        } else {
            $name = 'ERROR';
        }

        return $app['twig']->render('admin.html', array (
            'msg'   => 'Welcome to the admin page!',
            'token' => $name
        ));
    }

    /**
     * @return string
     */
    public function testAsterConnectionAction()
    {
        // Aster Cluster connection data
        $driver     = '{AsterDriver}';
        $server     = '192.168.100.100';
        $database   = 'recommendation';
        $user       = 'db_superuser';
        $pass       = 'db_superuser';

        // No changes needed from now on
        $connection_string = "Driver=$driver;Server=$server;Database=$database";
        // $connection_string = "DSN=testdsn";
        $conn = odbc_connect($connection_string,$user,$pass);

        if ($conn) {
            return "Connection established!";
        } else{
            return "Connection could not be established.";
        }
        /*
        return $app['twig']->render('admin.html', array (
            'msg'   => 'Welcome to the admin page!',
            'token' => $app['security.token_storage']->getToken(),
        ));
        */
    }
} 