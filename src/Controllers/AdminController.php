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
        $token = $app['security.token_storage']->getToken();

        if (null !== $token) {
            /** @var User */
            $user = $token->getUser();
            $name = $user->getUsername();
        } else {
            $name = 'ERROR';
        }

        $logged = 'TODO'; //$app['security.token_storage']->is_granted('IS_AUTHENTICATED_FULLY');

        return $app['twig']->render('admin.html', array (
            'msg'   => 'Welcome to the admin page!',
            'token' => $name,
            'logged' => $logged,
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