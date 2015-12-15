<?php

namespace Model;

use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{

    protected $email;
    protected $password;
    protected $enabled;
    protected $roles;

    public function __construct($email, $password, array $roles = array(['ROLE_USER']), $enabled = true)
    {
        if ('' === $email || null === $email) {
            throw new \InvalidArgumentException('The email cannot be empty.');
        }

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

