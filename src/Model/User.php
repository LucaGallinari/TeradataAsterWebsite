<?php

namespace Model;

use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    protected $username;
    protected $password;
    protected $email;
    protected $enabled;
    protected $roles;

    public function __construct($username, $password, $email, array $roles = array(['ROLE_USER']), $enabled = true)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->enabled = $enabled;
        $this->roles = $roles;
    }

    public function __toString()
    {
        if (is_null($this->username)) {
            return 'NULL';
        }
        return $this->getUsername();
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
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

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function eraseCredentials()
    {
        $this->username = null;
        $this->password = null;
        $this->enabled = false;

        return $this;
    }
}

