<?php
/**
 * UserProvider.php
 *
 * PHP version 5.5
 *
 * @category
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Repository;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use ODBC_Aster\ODBCAsterManager;
use App\Model\User;

class UserProvider implements UserProviderInterface {

    /** @var ODBCAsterManager */
    private $conn;

    public function __construct(ODBCAsterManager $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $results = $this->conn->executeQuery(
            "SELECT * FROM users WHERE email = '$username'"
        );

        if (!$user = $this->conn->fetch($results)) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }

        /*
        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }
        */
        return new User($user['email'], $user['password'], explode(',', $user['roles']), true);
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'App\Model\User';
        //return $class === 'Symfony\Component\Security\Core\User\User';
    }
}