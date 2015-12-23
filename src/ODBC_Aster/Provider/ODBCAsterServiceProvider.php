<?php
/**
 *
 */
namespace ODBC_Aster\Provider;

use ODBC_Aster\Repository\DBRecommendationRepository;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class ODBCAsterServiceProvider
 * @package ODBC_Aster\Provider
 */
class ODBCAsterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     * @return DBRecommendationRepository
     */
    public function register(Application $app)
    {
        if (!isset($app['odbc_aster.configs'])) {
            $app['odbc_aster.configs'] = array(
                'driver'   => '{AsterDriver}',      // Driver name put in the ODBC file
                'host'     => '192.168.100.100',    // IP or hostname of the DB
                'database' => 'recommendation',     // DB name
                'username' => 'db_superuser',
                'password' => 'db_superuser',
            );
        }
        $app['odbc_aster'] = $app->share(function ($app) {
            $dbRepository = new DBRecommendationRepository($app['odbc_aster.configs']);
            return $dbRepository;
        });
    }

    /**
     * @param Application $app
     * @codeCoverageIgnore
     */
    public function boot(Application $app)
    {
    }
}