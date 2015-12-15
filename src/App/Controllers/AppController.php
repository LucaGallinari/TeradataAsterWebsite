<?php
/**
 * AppController.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Controller;

use Silex\Application;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AppController {

    /**
     * @param Application $app
     * @return string
     */
    public function indexAction(Application $app)
    {
        /** @var $token TokenInterface */
        $token = $app['security.token_storage']->getToken();

        if (null !== $token) {
            $name = $token->getUser();
        } else {
            $name = 'ERROR';
        }

        return $app['twig']->render('app.twig', array (
            'token' => $name,
            'page'  => 'app',
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
    }
} 