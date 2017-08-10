<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Entity\Facebook;

/**
 * Create a link to an facebook.
 *
 * @category    Facebook
 */
class FacebookLink extends LinkAbstract
{
    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * @param Facebook $facebook
     * @param string $action
     * @param string $show
     * @param null $page
     * @param null $alternativeShow
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Facebook $facebook = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null
    ) {
        $this->setFacebook($facebook);
        $this->setAction($action);
        $this->setShow($show);
        $this->setPage($page);

        if (!$this->hasAccess($this->getFacebook(), FacebookAssertion::class, $this->getAction())) {
            return '';
        }

        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        if (!is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($page);
        }

        $this->setShowOptions(
            [
                'name' => $this->getFacebook()->getFacebook(),
            ]
        );
        $this->addRouterParam('id', $this->getFacebook()->getId());

        return $this->createLink();
    }

    /**
     * @return Facebook
     */
    public function getFacebook()
    {
        if (is_null($this->facebook)) {
            $this->facebook = new Facebook();
        }

        return $this->facebook;
    }

    /**
     * @param Facebook $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/facebook/new');
                $this->setText($this->translate("txt-new-facebook"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/facebook/list');
                $this->setText($this->translate("txt-list-facebooks"));

                foreach ($this->getServiceManager()->get('application')->getMvcEvent()->getRequest()->getQuery() as $key =>
                    $param) {
                    $this->addQueryParam($key, $param);
                }
                $this->addQueryParam('page', $this->getPage());

                break;
            case 'edit':
                $this->setRouter('zfcadmin/facebook/edit');
                $this->setText(sprintf($this->translate("txt-edit-facebook-%s"), $this->getFacebook()->getFacebook()));
                break;
            case 'view-community':
                $this->setRouter('community/contact/facebook/facebook');
                $this->setText(sprintf($this->translate("txt-view-facebook-%s"), $this->getFacebook()->getFacebook()));
                break;
            case 'send-message':
                $this->setRouter('community/contact/facebook/send-message');
                $this->setText(
                    sprintf(
                        $this->translate("txt-send-message-to-%s"),
                        $this->getFacebook()->getFacebook()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/facebook/view');
                $this->setText(sprintf($this->translate("txt-view-facebook-%s"), $this->getFacebook()->getFacebook()));
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
