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

use Laminas\Json\Json;

final class GenericUser
{
    private string $id;
    private string $firstName;
    private string $lastName;
    private string $email;

    public function __construct(\stdClass $result)
    {
        $this->id        = $result->id;
        $this->firstName = $result->first_name;
        $this->lastName  = $result->last_name;
        $this->email     = $result->email;
    }

    public static function fromJson(string $jsonString): GenericUser
    {
        return new self(Json::decode($jsonString));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }
}
