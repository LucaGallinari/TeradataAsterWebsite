<?php

namespace ODBC_Aster\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ODBC_Aster\ODBCAsterManager;

class ODBCAsterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     * @return ODBCAsterManager
     */
    public function register(Application $app)
    {
        if (!isset($app['odbc_aster.configs'])) {
            $app['odbc_aster.configs'] = array(
                'driver'    => 'pdo_mysql',
                'host'      => 'localhost',
                'database'  => 'aster_website',
                'username'  => 'phpmyadmin',
                'password'  => 'phpmyadmin',
            );
        }
        $app['odbc_aster'] = $app->share(function ($app) {
            $odbc = new ODBCAsterManager($app['odbc_aster.configs']);
            return $odbc;
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