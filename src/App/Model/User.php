<?php
/**
 * User.php
 *
 * PHP version 5.5
 *
 * @category App
 * @package  TeradataAsterWebsite
 * @author   Luca Gallinari <luke.gallinari@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     TOINSERTLINK
 */

namespace App\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Model
 */
final class User implements UserInterface
{

    protected $id;
    protected $email;
    protected $password;
    protected $enabled;
    protected $roles;

    public function __construct($id = null, $email, $password, array $roles = array('ROLE_USER'), $enabled = true)
    {
        if ('' === $email || null === $email) {
            throw new \InvalidArgumentException('The email cannot be empty.');
        }

        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->roles = $roles;
    }

    public function __toString()
    {
        if (is_null($this->email)) {
            return 'NULL';
        }
        return $this->getEmail();
    }

    public function __toDBInsertString()
    {
        return sprintf(
            "'%s', '%s', '%s', '%s'",
            $this->email,
            $this->password,
            implode(',', $this->roles),
            $this->enabled
        );
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setUsername($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRoles($roles)
    {
        if (is_array($roles)) {
            $this->roles = $roles;
        }
    }

    public function setEnabled($enable)
    {
        $this->enabled = $enable;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        $this->password = null;
        $this->enabled = false;

        return $this;
    }
}

