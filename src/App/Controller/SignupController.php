<?php
/**
 * SignupController.php
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
use App\Repository\UserProvider;
use Symfony\Component\HttpFoundation\Request;

class SignupController {
    /**
     * @var Application;
     */
    protected $app = null;

    /**
     * @param Application $app
     */
    public function indexAction(Application $app)
    {
        return $app['twig']->render('signup.twig', array (
            'page'  => 'signup',
        ));
    }

    /**
     * @param Request $req
     * @param Application $app
     */
    public function signupAction(Request $req, Application $app)
    {
        $signupError = '';
        $message = '';

        if (!is_null($req->request->get('action'))) {
            $username = isset($_POST['_username']) ? trim($_POST['_username']) : false;

            if ($username !== false && $username != '') {
                $password = isset($_POST['_password']) ? $_POST['_password'] : false;

                if ($password !== false && $password != '') {
                    $password_check = isset($_POST['_password_check']) ? $_POST['_password_check'] : false;

                    if ($password_check !== false && $password_check == $password) {
                        /**@var $userProvider UserProvider */
                        $userProvider = new UserProvider($app['odbc_aster'], $app['security.encoder.digest']);
                        //$userProvider = $app['security.firewalls']['app']['users'];

                        if ($userProvider->signupUser($username, $password)) {
                            $message = 'The registration has ended successfully. Now you can login!';

                        } else {
                            $signupError = 'The user is already present. Please change the email address!';
                        }
                    } else {
                        $signupError = 'Password confirmation does not match the password inserted.';
                    }
                } else {
                    $signupError = 'Invalid Password (probably empty).';
                }
            } else {
                $signupError = 'Invalid Email.';
            }
        }

        return $app['twig']->render('signup.twig', array (
            'signupError'   => $signupError,
            'message'       => $message,
            'page'          => 'signup',
        ));
    }

} 