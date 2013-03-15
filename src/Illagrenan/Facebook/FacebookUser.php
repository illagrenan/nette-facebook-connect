<?php

/**
 * Tento soubor je součástí rozšíření "Nette Facebook Connect"
 * @link https://github.com/illagrenan/nette-facebook-connect
 * 
 * Copyright (c) 2013 Václav Dohnal, http://www.vaclavdohnal.cz
 */

namespace Illagrenan\Facebook;

/**
 * Data aktuálně přihlášeného a autorizovaného uživatele.
 * 
 * @author Vašek Dohnal http://www.vaclavdohnal.cz
 */
final class FacebookUser extends \Nette\Object
{

    /**
     * The user's Facebook ID
     * @var int
     */
    private $id;

    /**
     * The user's first name
     * @var string 
     */
    private $firstName;

    /**
     * The user's last name
     * @var string
     */
    private $lastName;

    /**
     * The URL of the profile for the user on Facebook
     * @var string URL 
     */
    private $profileLink;

    /**
     * The user's Facebook username
     * @var type 
     */
    private $username;

    /**
     * @var \Illagrenan\Facebook\UserGender
     */
    private $gender;

    /**
     * The user's locale
     * @var string
     * @link http://developers.facebook.com/docs/internationalization/
     */
    private $locale;

    /**
     * @var string
     */
    private $email;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $profileLink
     * @param string $username
     * @param string $gender
     * @param string $locale
     */
    public function __construct($id, $firstName, $lastName, $profileLink, $username, $gender, $locale, $email = NULL)
    {
        $this->id          = (int) $id;
        $this->firstName   = (string) $firstName;
        $this->lastName    = (string) $lastName;
        $this->profileLink = (string) $profileLink;
        $this->username    = (string) $username;
        $this->gender      = (string) $gender;
        $this->locale      = (string) $locale;
        $this->email       = $email;
    }

    public function __toString()
    {
        return "FBUser: " . $this->getFullName() . ", " . $this->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string The user's full name
     */
    public function getFullName()
    {
        return ($this->firstName . " " . $this->lastName);
    }

    /**
     * @return string
     */
    public function getProfileLink()
    {
        return $this->profileLink;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return \Illagrenan\Facebook\UserGender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

}

final class UserGender extends \Nette\Object
{

    const MALE    = "male";
    const FEMALE  = "female";
    const UNKNOWN = "unknown";

    private function __construct()
    {
        
    }

}
