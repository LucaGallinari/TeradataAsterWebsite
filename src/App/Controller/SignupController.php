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

        if ($app['odbc_aster'] !== false) {

            if (!is_null($req->request->get('action'))) {
                $username = $req->request->get('_username', false);

                if ($username !== false) {
                    $password = $req->request->get('_password', false);

                    if ($password !== false) {
                        $password_check = $req->request->get('_password_check', false);

                        if ($password_check !== false && $password_check == $password) {
                            /**@var $userProvider UserProvider */
                            $userProvider = new UserProvider($app['odbc_aster'], $app['security.encoder.digest']);

                            $userid = uniqid(rand().'_');

                            if ($userProvider->signupUser($userid, $username, $password)) {
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

        } else {
            $signupError = 'Could not connect to the database! Please retry soon.';
        }

        return $app['twig']->render('signup.twig', array (
            'signupError'   => $signupError,
            'message'       => $message,
            'page'          => 'signup',
        ));
    }

} 