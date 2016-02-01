<?php
/**
 * config.php
 *
 * PHP version 5.6
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

// These are the setting for accessing Twitter APIs
$settings = array(
    'oauth_access_token'        => "FILL",
    'oauth_access_token_secret' => "FILL",
    'consumer_key'              => "FILL",
    'consumer_secret'           => "FILL"
);

// CONFIG
// Base subdir of the project relative to the virtual host root dir.
// In my case it's:     http://localhost:8005/TeradataAsterWebsite/
// If your virtual host point at the localhost root dir 'http://localhost/' you need to leave this variable empty
$__BASEDIR__ = "/TeradataAsterWebsite";

/*
 * IMPORTANT:
 * In the 'login.twig' and 'base.twig' i used those path:
 *  - path('TeradataAsterWebsite_app_login_check')
 *  - path('TeradataAsterWebsite_app_logout')
 * Please modify these strings based on the $__BASEDIR__ variable.
 * 
 * If your $__BASEDIR__ is empty they must be:
 *  - path('app_login_check')
 *  - path('app_logout')
 */