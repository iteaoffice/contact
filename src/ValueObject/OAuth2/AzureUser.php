<?php

/**
 * Jield BV copyright message placeholder
 *
 * @category   Admin
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <info@jield.nl>
 * @copyright  Copyright (c) 2018 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Contact\ValueObject\OAuth2;

final class AzureUser
{
    private $id;
    private $displayName;
    private $givenName;
    private $mail;
    private $jobTitle;
    private $officeLocation;
    private $surname;

    public function __construct(array $result)
    {
        $this->id             = $result['id'] ?? null;
        $this->displayName    = $result['displayName'] ?? null;
        $this->givenName      = $result['givenName'] ?? null;
        $this->mail           = $result['mail'] ?? null;
        $this->jobTitle       = $result['jobTitle'] ?? null;
        $this->officeLocation = $result['officeLocation'] ?? null;
        $this->surname        = $result['surname'] ?? null;
    }


    public function getFirstName(): ?string
    {
        return $this->givenName;
    }

    public function getEmail(): ?string
    {
        return $this->mail;
    }

    public function getLastName(): ?string
    {
        return $this->surname;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
