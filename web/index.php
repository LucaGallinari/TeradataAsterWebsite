<?php
/**
 * index.php
 *
 * PHP version 5.6
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Silex\Provider;
use ODBC_Aster\Provider\ODBCAsterServiceProvider;
use App\Repository\UserProvider;

$app = new Silex\Application();

// options
$app['debug'] = "true";
$__PROJDIR__ = __DIR__."/../src";

// twitter config
$app['twitter.config'] = $settings;

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// ODBC Aster
$app->register(new ODBCASterServiceProvider(), array(
    'odbc_aster.configs' => array(
        'driver'   => '{AsterDriver}',
        'host'     => '192.168.1.200',
        'database' => 'beehive',
        'username' => 'db_superuser',
        'password' => 'db_superuser',
    ),
));

// Security
$app->register(new Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'app' => array(
            'pattern' => '^'.$__BASEDIR__.'/.*$',
            'form' => array(
                'login_path' => $__BASEDIR__.'/',
                'check_path' => $__BASEDIR__.'/app/login_check',
                'default_target_path' => $__BASEDIR__.'/app/',
                'always_use_default_target_path' => true
            ),
            'logout' => array(
                'logout_path' => $__BASEDIR__.'/app/logout',
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
        array('^'.$__BASEDIR__.'/app/', 'ROLE_USER', 'http')
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
$app['asset_path'] = $__BASEDIR__.'/src/App/Resources';


// Routing
$app->get($__BASEDIR__.'/', 'App\\Controller\\LoginController::indexAction')->bind('login');
$app->get($__BASEDIR__.'/signup/', 'App\\Controller\\SignupController::indexAction')->bind('signup');
$app->post($__BASEDIR__.'/signup/', 'App\\Controller\\SignupController::signupAction')->bind('signup_do');
$app->get($__BASEDIR__.'/app/', 'App\\Controller\\AppController::indexAction')->bind('app');
$app->get($__BASEDIR__.'/app/my-events/', 'App\\Controller\\EventController::indexAction')->bind('myevents');
$app->post($__BASEDIR__.'/app/my-events/', 'App\\Controller\\EventController::addAction')->bind('myevents_add');
$app->get($__BASEDIR__.'/app/my-events/del', 'App\\Controller\\EventController::delAction')->bind('myevents_del');
$app->get($__BASEDIR__.'/app/event/', 'App\\Controller\\EventController::viewAction')->bind('event');
$app->get($__BASEDIR__.'/app/event/search', 'App\\Controller\\EventController::searchAction')->bind('event_search');

$app->get($__BASEDIR__.'/app/aster/test', 'App\\Controller\\AppController::testAsterConnectionAction')->bind('app_aster_test');

$app->get($__BASEDIR__.'/app/events/interest/add/', 'App\\Controller\\EventInterestController::addAction')->bind('events_interest_add');

$app->run();