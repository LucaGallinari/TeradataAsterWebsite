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

use Controller\AdminController;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Silex\Provider;

$app = new Silex\Application();
$__PROJDIR__ = __DIR__."/../src";

// options
$app['debug'] = "true";

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Doctrine
$app->register(new Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        // 'port'   => '24',
        'dbname'    => 'aster_website',
        'user'      => 'phpmyadmin',
        'password'  => 'phpmyadmin',
    ),
));

// Security
$app->register(new Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin/',
            'form' => array(
                'login_path' => '/',
                'check_path' => '/admin/login_check',
                'default_target_path' => '/admin',
            ),
            'logout' => array(
                'logout_path' => '/admin/logout',
                'invalidate_session' => true
            ),
            'users' => $app->share(function () use ($app) {
                return new Repository\UserProvider($app['db']);
            }),
        ),
    ),
    'security.encoder.digest' => $app->share(function() {
        // return new Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(12);
        return new MessageDigestPasswordEncoder('sha1', false, 1);
    })
));

// Twig
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => $__PROJDIR__.'/Views',
));


// Routing
$app->get('/', 'Controller\\LoginController::indexAction')->bind('login');
$app->get('/admin', 'Controller\\AdminController::indexAction')->bind('admin');
/*
$app['controllers.admin'] = $app->share(function() use ($app) {
    return new AdminController($app);
});
$app->get('/admin', "controllers.admin:indexAction");
*/
$app->get('/admin/aster/test', 'Controller\\AdminController::testAsterConnectionAction')->bind('admin_aster_test');


$app->run();