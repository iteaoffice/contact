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

namespace Contact\View\Helper;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use BjyAuthorize\Service\Authorize;
use Contact\Acl\Assertion\AbstractAssertion;
use Contact\Acl\Assertion\AssertionAbstract;
use Contact\Entity\AbstractEntity;
use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Entity\Note;
use Contact\Entity\Phone;
use Contact\Entity\Selection;
use Exception;
use InvalidArgumentException;
use Zend\Router\Http\RouteMatch;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;
use function in_array;
use function is_null;

/**
 * Class LinkAbstract.
 */
abstract class LinkAbstract extends AbstractViewHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $container;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;
    /**
     * @var string Text to be placed as title or as part of the linkContent
     */
    protected $text;
    /**
     * @var string
     */
    protected $router;
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $show;
    /**
     * @var string
     */
    protected $alternativeShow;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $fragment = null;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $queryParams = [];
    /**
     * @var array content of the link (will be imploded during creation of the link)
     */
    protected $linkContent = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var array
     */
    protected $showOptions = [];
    /**
     * @var int
     */
    protected $page;
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var Address
     */
    protected $address;
    /**
     * @var Note
     */
    protected $note;
    /**
     * @var Phone
     */
    protected $phone;
    /**
     * @var Selection
     */
    protected $selection;

    /**
     * This function produces the link in the end.
     *
     * @return string
     */
    public function createLink(): string
    {
        /**
         * @var $url Url
         */
        $url = $this->getHelperPluginManager()->get('url');
        /**
         * @var $serverUrl ServerUrl
         */
        $serverUrl = $this->getHelperPluginManager()->get('serverUrl');
        $this->linkContent = [];
        $this->classes = [];

        $this->parseAction();
        $this->parseShow();
        if ('social' === $this->getShow()) {
            return $serverUrl->__invoke() . $url($this->router, $this->routerParams);
        }
        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl() . $url(
                $this->router,
                $this->routerParams,
                is_null($this->getFragment()) ? [] : ['fragment' => $this->getFragment()]
            ),
            htmlentities((string)$this->text),
            implode(' ', $this->classes),
            in_array($this->getShow(), ['icon', 'button', 'alternativeShow']) ? implode('', $this->linkContent)
                : htmlentities(implode('', $this->linkContent))
        );
    }

    /**
     *
     */
    public function parseAction(): void
    {
        $this->action = null;
    }

    /**
     * @throws Exception
     */
    public function parseShow(): void
    {
        switch ($this->getShow()) {
            case 'icon':
            case 'button':
                switch ($this->getAction()) {
                    case 'edit-profile':
                    case 'edit':
                    case 'edit-admin':
                        $this->addLinkContent('<i class="fa fa-pencil-square-o"></i>');
                        break;
                    case 'change-password':
                        $this->addLinkContent('<i class="fa fa-key"></i>');
                        break;
                    case 'view-admin':
                    case 'view':
                        $this->addLinkContent('<i class="fa fa-user"></i>');
                        break;
                    case 'edit-contacts':
                        $this->addLinkContent('<i class="fa fa-users"></i>');
                        break;
                    case 'add-contact':
                    case 'new':
                    case 'add-project':
                        $this->addLinkContent('<i class="fa fa-plus"></i>');
                        break;
                    case 'export-excel':
                    case 'export-csv':
                        $this->addLinkContent('<i class="fa fa-file-excel-o"></i>');
                        break;
                    case 'import':
                        $this->addLinkContent('<i class="fa fa-upload" aria-hidden="true"></i>');
                        break;
                    case 'send-message':
                        $this->addLinkContent('<i class="fa fa-envelope"></i>');
                        break;

                    default:
                        $this->addLinkContent('<i class="fa fa-link"></i>');
                        break;
                }

                if ($this->getShow() === 'button') {
                    $this->addLinkContent(' ' . $this->getText());
                    $this->addClasses('btn btn-primary');
                }

                break;
            case 'text':
                $this->addLinkContent($this->getText());
                break;
            case 'paginator':
                if (null === $this->getAlternativeShow()) {
                    throw new InvalidArgumentException(
                        sprintf("this->alternativeShow cannot be null for a paginator link")
                    );
                }
                $this->addLinkContent($this->getAlternativeShow());
                break;
            case 'social':
                /*
                 * Social is treated in the createLink function, no content needs to be created
                 */

                return;
            default:
                if (!array_key_exists($this->getShow(), $this->showOptions)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            "The option \"%s\" should be available in the showOptions array, only \"%s\" are available",
                            $this->getShow(),
                            implode(', ', array_keys($this->showOptions))
                        )
                    );
                }
                $this->addLinkContent($this->showOptions[$this->getShow()]);
                break;
        }
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param string $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param $linkContent
     *
     * @return $this
     */
    public function addLinkContent($linkContent)
    {
        if (!is_array($linkContent)) {
            $linkContent = [$linkContent];
        }
        foreach ($linkContent as $content) {
            $this->linkContent[] = $content;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }
        foreach ($classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternativeShow()
    {
        return $this->alternativeShow;
    }

    /**
     * @param string $alternativeShow
     */
    public function setAlternativeShow($alternativeShow)
    {
        $this->alternativeShow = $alternativeShow;
    }

    /**
     * @return array
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param array $fragment
     *
     * @return LinkAbstract
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Reset the params.
     */
    public function resetRouterParams()
    {
        $this->routerParams = [];
    }

    /**
     * @param $showOptions
     */
    public function setShowOptions($showOptions)
    {
        $this->showOptions = $showOptions;
    }

    /**
     * @param AbstractEntity $entity
     * @param string         $assertion
     * @param string         $action
     *
     * @return bool
     */
    public function hasAccess(AbstractEntity $entity, $assertion, $action)
    {
        $assertion = $this->getAssertion($assertion);
        if (!is_null($entity) && !$this->getAuthorizeService()->getAcl()->hasResource($entity)) {
            $this->getAuthorizeService()->getAcl()->addResource($entity);
            $this->getAuthorizeService()->getAcl()->allow([], $entity, [], $assertion);
        }
        if (!$this->isAllowed($entity, $action)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $assertion
     *
     * @return AbstractAssertion
     */
    public function getAssertion($assertion)
    {
        return $this->getServiceManager()->get($assertion);
    }

    /**
     * @return Authorize
     */
    public function getAuthorizeService()
    {
        return $this->getServiceManager()->get('BjyAuthorize\Service\Authorize');
    }

    /**
     * @param null|AbstractEntity $resource
     * @param string              $privilege
     *
     * @return bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        /**
         * @var $isAllowed IsAllowed
         */
        $isAllowed = $this->getHelperPluginManager()->get('isAllowed');

        return $isAllowed($resource, $privilege);
    }

    /**
     * Add a parameter to the list of parameters for the router.
     *
     * @param string $key
     * @param        $value
     * @param bool   $allowNull
     */
    public function addRouterParam($key, $value, $allowNull = true)
    {
        if (!$allowNull && null === $value) {
            throw new InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (null !== $value) {
            $this->routerParams[$key] = $value;
        }
    }

    /**
     * Add a parameter to the list of parameters for the query.
     *
     * @param string $key
     * @param        $value
     * @param bool   $allowNull
     */
    public function addQueryParam($key, $value, $allowNull = true)
    {
        if (!$allowNull && null === $value) {
            throw new InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (null !== $value) {
            $this->queryParams[$key] = $value;
        }
    }

    /**
     * @return string
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param string $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getRouterParams()
    {
        return $this->routerParams;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return LinkAbstract
     */
    public function setPage($page)
    {
        $this->page = $page;

        return true;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return LinkAbstract
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        if (is_null($this->contact)) {
            $this->contact = new Contact();
        }

        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return LinkAbstract
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        if (is_null($this->address)) {
            $this->address = new Address();
        }

        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return LinkAbstract
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Note
     */
    public function getNote()
    {
        if (is_null($this->note)) {
            $this->note = new Note();
        }

        return $this->note;
    }

    /**
     * @param Note $note
     *
     * @return LinkAbstract
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Phone
     */
    public function getPhone()
    {
        if (is_null($this->phone)) {
            $this->phone = new Phone();
        }

        return $this->phone;
    }

    /**
     * @param Phone $phone
     *
     * @return LinkAbstract
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Selection
     */
    public function getSelection()
    {
        if (is_null($this->selection)) {
            $this->selection = new Selection();
        }

        return $this->selection;
    }

    /**
     * @param Selection $selection
     *
     * @return LinkAbstract
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;

        return $this;
    }
}
