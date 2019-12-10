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

use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Entity\Facebook;
use Exception;

/**
 * Create a link to an facebook.
 *
 * @category    Facebook
 */
final class FacebookLink extends LinkAbstract
{
    protected Facebook $facebook;

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
        if ($alternativeShow !== null) {
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

    public function getFacebook()
    {
        if ($this->facebook === null) {
            $this->facebook = new Facebook();
        }

        return $this->facebook;
    }

    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/facebook/new');
                $this->setText($this->translate('txt-new-facebook'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/facebook/list');
                $this->setText($this->translate('txt-list-facebooks'));
                $this->addQueryParam('page', $this->getPage());

                break;
            case 'edit':
                $this->setRouter('zfcadmin/facebook/edit');
                $this->setText(sprintf($this->translate('txt-edit-facebook-%s'), $this->getFacebook()->getFacebook()));
                break;
            case 'view-community':
                $this->setRouter('community/contact/facebook/view');
                $this->setText(sprintf($this->translate('txt-view-facebook-%s'), $this->getFacebook()->getFacebook()));
                break;
            case 'send-message':
                $this->setRouter('community/contact/facebook/send-message');
                $this->setText(
                    sprintf(
                        $this->translate('txt-send-message-to-%s'),
                        $this->getFacebook()->getFacebook()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/facebook/view');
                $this->setText(sprintf($this->translate('txt-view-facebook-%s'), $this->getFacebook()->getFacebook()));
                break;
            default:
                throw new Exception(sprintf('%s is an incorrect action for %s', $this->getAction(), __CLASS__));
        }
    }
}
