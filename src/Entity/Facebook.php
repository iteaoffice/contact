<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Admin\Entity\Access;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * Entity for the Facebook.
 *
 * @ORM\Table(name="facebook")
 * @ORM\Entity(repositoryClass="Contact\Repository\Facebook")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_facebook")
 *
 * @category    Contact
 */
class Facebook extends AbstractEntity
{
    public const DISPLAY_NONE = 1;
    public const DISPLAY_ORGANISATION = 2;
    public const DISPLAY_COUNTRY = 3;
    public const DISPLAY_POSITION = 4;
    public const DISPLAY_PROJECTS = 5;
    public const SHOW_EMAIL_NO = 1;
    public const SHOW_EMAIL_MEMBER = 2;
    public const SHOW_EMAIL_ALL = 3;
    public const SHOW_PHONE_NO = 1;
    public const SHOW_PHONE_MEMBER = 2;
    public const SHOW_PHONE_ALL = 3;
    public const SHOW_MOBILE_PHONE_MEMBER = 4;
    /**
     * Constant for public = 0 (not public).
     */
    public const CAN_NOT_SEND_MESSAGE = 0;
    /**
     * Constant for public = 1 (hidden).
     */
    public const CAN_SEND_MESSAGE = 1;
    /**
     * Constant for public = 0 (not public).
     */
    public const NOT_PUBLIC = 0;
    /**
     * Constant for public = 1 (hidden).
     */
    public const IS_PUBLIC = 1;

    protected static array $displayTemplates
        = [
            self::DISPLAY_NONE         => 'txt-empty',
            self::DISPLAY_ORGANISATION => 'txt-organisation',
            self::DISPLAY_COUNTRY      => 'txt-country',
            self::DISPLAY_POSITION     => 'txt-position',
            self::DISPLAY_PROJECTS     => 'txt-projects',
        ];

    protected static array $showEmailTemplates
        = [
            self::SHOW_EMAIL_NO     => 'txt-hide-email',
            self::SHOW_EMAIL_MEMBER => 'txt-show-email-to-members',
            self::SHOW_EMAIL_ALL    => 'txt-show-email-to-all',
        ];

    protected static array $showPhoneTemplates
        = [
            self::SHOW_PHONE_NO            => 'txt-hide-phone',
            self::SHOW_PHONE_MEMBER        => 'txt-show-phone-to-members',
            self::SHOW_MOBILE_PHONE_MEMBER => 'txt-show-mobile-phone-to-members',
            self::SHOW_PHONE_ALL           => 'txt-show-phone-to-all',
        ];

    protected static array $canSendMessageTemplates
        = [
            self::CAN_NOT_SEND_MESSAGE => 'txt-cannot-send-message',
            self::CAN_SEND_MESSAGE     => 'txt-can-send-message',
        ];

    protected static array $publicTemplates
        = [
            self::NOT_PUBLIC => 'txt-not-public',
            self::IS_PUBLIC  => 'txt-public',
        ];
    /**
     * @ORM\Column(name="facebook_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="facebook", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-facebook"})
     *
     * @var string
     */
    private $facebook;
    /**
     * @ORM\Column(name="public", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"publicTemplates"})
     * @Annotation\Attributes({"label":"txt-public"})
     *
     * @var int
     */
    private $public;
    /**
     * @ORM\Column(name="can_send_message", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"canSendMessageTemplates"})
     * @Annotation\Attributes({"label":"txt-can-send-message", "required":"true","help-block": "txt-can-send-message-explanation"})
     *
     * @var int
     */
    private $canSendMessage;
    /**
     * @ORM\Column(name="from_clause", type="text", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"placeholder":"txt-from-clause"})
     * @Annotation\Options({"label":"txt-from-clause","help-block": "txt-from-clause-explanation"})
     *
     * @var string
     */
    private $fromClause;
    /**
     * @ORM\Column(name="where_clause", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"placeholder":"txt-where-clause"})
     * @Annotation\Options({"label":"txt-where-clause","help-block": "txt-where-clause-explanation"})
     *
     * @var string
     */
    private $whereClause;
    /**
     * @ORM\Column(name="orderby_clause", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"placeholder":"txt-orderby-clause"})
     * @Annotation\Options({"label":"txt-orderby-clause","help-block": "txt-orderby-clause-explanation"})
     *
     * @var string
     */
    private $orderbyClause;
    /**
     * @ORM\Column(name="contact_key", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"placeholder":"txt-contact-key"})
     * @Annotation\Options({"label":"txt-contact-key","help-block": "txt-contact-key-explanation"})
     *
     * @var string
     */
    private $contactKey;
    /**
     * @ORM\Column(name="com_extra", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"displayTemplates"})
     * @Annotation\Options({"label":"txt-title","help-block": "txt-title-explanation"})
     *
     * @var string
     */
    private $title;
    /**
     * @ORM\Column(name="com_sub", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"displayTemplates"})
     * @Annotation\Options({"label":"txt-sub-title","help-block": "txt-sub-title-explanation"})
     *
     * @var int
     */
    private $subtitle;
    /**
     * @ORM\Column(name="show_email", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showEmailTemplates"})
     * @Annotation\Options({"label":"txt-show-email-title","help-block": "txt-show-email-explanation"})
     *
     * @var string
     */
    private $showEmail;
    /**
     * @ORM\Column(name="show_phone", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showPhoneTemplates"})
     * @Annotation\Options({"label":"txt-show-phone-title","help-block": "txt-show-phone-explanation"})
     *
     * @var string
     */
    private $showPhone;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", inversedBy="facebook")
     * @ORM\OrderBy({"access"="ASC"})
     * @ORM\JoinTable(name="facebook_access",
     *            joinColumns={@ORM\JoinColumn(name="facebook_id", referencedColumnName="facebook_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"Admin\Entity\Access",
     *      "find_method":{
     *          "name":"findWithoutSelection",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "access":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-facebook-access-label"})
     * @Annotation\Options({"help-block":"txt-facebook-access-help-block"})
     * @Annotation\Attributes({"multiple":true,"data-actions-box":true})
     *
     * @var Access[]|Collections\ArrayCollection
     */
    private $access;

    public function __construct()
    {
        $this->access = new Collections\ArrayCollection();
    }

    public static function getDisplayTemplates(): array
    {
        return self::$displayTemplates;
    }

    public static function getShowEmailTemplates(): array
    {
        return self::$showEmailTemplates;
    }

    public static function getShowPhoneTemplates(): array
    {
        return self::$showPhoneTemplates;
    }

    public static function getCanSendMessageTemplates(): array
    {
        return self::$canSendMessageTemplates;
    }

    public static function getPublicTemplates(): array
    {
        return self::$publicTemplates;
    }

    public function __toString(): string
    {
        return (string)$this->facebook;
    }

    public function addAccess(Collections\Collection $collection): void
    {
        foreach ($collection as $access) {
            $this->access->add($access);
        }
    }

    public function removeAccess(Collections\Collection $collection): void
    {
        foreach ($collection as $access) {
            $this->access->removeElement($access);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Facebook
    {
        $this->id = $id;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook($facebook): Facebook
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getPublic(bool $textual = false)
    {
        if ($textual) {
            return self::$publicTemplates[$this->public];
        }

        return $this->public;
    }

    public function setPublic($public): Facebook
    {
        $this->public = $public;

        return $this;
    }

    public function getFromClause(): ?string
    {
        return $this->fromClause;
    }

    public function setFromClause($fromClause): Facebook
    {
        $this->fromClause = $fromClause;

        return $this;
    }

    public function getWhereClause(): ?string
    {
        return $this->whereClause;
    }

    public function setWhereClause($whereClause): Facebook
    {
        $this->whereClause = $whereClause;

        return $this;
    }

    public function getOrderbyClause(): ?string
    {
        return $this->orderbyClause;
    }

    public function setOrderbyClause($orderbyClause)
    {
        $this->orderbyClause = $orderbyClause;

        return $this;
    }

    public function getContactKey()
    {
        return $this->contactKey;
    }

    public function setContactKey($contactKey)
    {
        $this->contactKey = $contactKey;

        return $this;
    }

    public function getTitle(bool $textual = false)
    {
        if ($textual) {
            return self::$displayTemplates[$this->title];
        }

        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(bool $textual = false)
    {
        if ($textual) {
            return self::$displayTemplates[$this->subtitle];
        }

        return $this->subtitle;
    }

    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getCanSendMessage(bool $textual = false)
    {
        if ($textual) {
            return self::$canSendMessageTemplates[$this->canSendMessage];
        }

        return $this->canSendMessage;
    }

    public function setCanSendMessage($canSendMessage)
    {
        $this->canSendMessage = $canSendMessage;

        return $this;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess($access): Facebook
    {
        $this->access = $access;

        return $this;
    }

    public function getShowEmail(bool $textual = false)
    {
        if ($textual) {
            return self::$showEmailTemplates[$this->showEmail];
        }

        return $this->showEmail;
    }

    public function setShowEmail($showEmail): Facebook
    {
        $this->showEmail = $showEmail;

        return $this;
    }

    public function getShowPhone(bool $textual = false)
    {
        if ($textual) {
            return self::$showPhoneTemplates[$this->showPhone];
        }

        return $this->showPhone;
    }

    public function setShowPhone($showPhone)
    {
        $this->showPhone = $showPhone;

        return $this;
    }
}
