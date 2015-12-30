<?php
/**
 * index.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Silex\Provider;
use ODBC_Aster\Provider\ODBCAsterServiceProvider;
use App\Repository\UserProvider;

$app = new Silex\Application();
$__PROJDIR__ = __DIR__."/../src";

// options
$app['debug'] = "true";

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// ODBC Aster
$app->register(new ODBCASterServiceProvider(), array(
    'odbc_aster.configs' => array(
        'driver'   => '{AsterDriver}',
        'host'     => '192.168.100.100',
        'database' => 'beehive',
        'username' => 'db_superuser',
        'password' => 'db_superuser',
    ),
));

// Security
$app->register(new Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'app' => array(
            'pattern' => '^/.*$',
            'form' => array(
                'login_path' => '/',
                'check_path' => '/app/login_check',
                'default_target_path' => '/app/',
                'always_use_default_target_path' => true
            ),
            'logout' => array(
                'logout_path' => '/app/logout',
                'invalidate_session' => true
            ),
            'anonymous' => true,
            'users' => $app->share(function () use ($app) {
                if ($app['odbc_aster'] === false) {
                    return new UserProvider();
                }
                return new UserProvider($app['odbc_aster'], $app['security.encoder.digest']);
            }),
        ),
    ),
    'security.access_rules' => array (
        array('^/app/$', 'ROLE_USER', 'http')
    ),
    'security.encoder.digest' => $app->share(function() {
        // return new Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(12);
        return new MessageDigestPasswordEncoder('sha1', false, 1);
    }),
    'security.role_hierarchy' => array (
        'ROLE_ADMIN' => array('ROLE_USER'),
    )
));

// Twig
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => $__PROJDIR__.'/App/Views',
));

// Assets
$app['asset_path'] = '/src/App/Resources';


// Routing
$app->get('/', 'App\\Controller\\LoginController::indexAction')->bind('login');
$app->get('/signup', 'App\\Controller\\SignupController::indexAction')->bind('signup');
$app->post('/signup', 'App\\Controller\\SignupController::signupAction')->bind('signup_do');
$app->get('/app/', 'App\\Controller\\AppController::indexAction')->bind('app');
$app->get('/app/aster/test', 'App\\Controller\\AppController::testAsterConnectionAction')->bind('app_aster_test');

$app->get('/event/interest/add/', 'App\\Controller\\EventInterestController::addAction')->bind('event_interest_add');

$app->run();