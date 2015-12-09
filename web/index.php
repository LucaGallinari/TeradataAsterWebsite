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

$app = new Silex\Application();

// options
$app['debug'] = "true";

// routing
$app->get('/', function () use ($app) {

    // Replace the value of these variables with your own data
    $driver     = '{AsterDriver}';
    $server     = '192.168.100.100';
    $database   = 'beehive';
    $user       = 'beehive';
    $pass       = 'beehive';

    // No changes needed from now on
    $connection_string = "Driver=$driver;Server=$server;Database=$database";
    // $connection_string = "DSN=testdsn";
    $conn = odbc_connect($connection_string,$user,$pass);

    if ($conn) {
        echo "Connection established.";
    } else{
        die("Connection could not be established.");
    }

});

$app->run();