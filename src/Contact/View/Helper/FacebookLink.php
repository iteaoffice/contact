<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\View\Helper;

use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Entity\Facebook;

/**
 * Create a link to an facebook
 *
 * @category    Facebook
 * @package     View
 * @subpackage  Helper
 */
class FacebookLink extends LinkAbstract
{
    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * @param Facebook $facebook
     * @param string   $action
     * @param string   $show
     * @param null     $page
     * @param null     $alternativeShow
     *
     * @return string
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

        if (!$this->hasAccess(
            $this->getFacebook(),
            FacebookAssertion::class,
            $this->getAction()
        )
        ) {
            return '';
        }

        /**
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
     * @throws \Exception
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/facebook-manager/new');
                $this->setText($this->translate("txt-new-facebook"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/facebook-manager/list');
                $this->setText($this->translate("txt-list-facebooks"));

                foreach ($this->getServiceLocator()->get('application')->getMvcEvent()->getRequest()->getQuery(
                ) as $key => $param) {
                    $this->addQueryParam($key, $param);
                }
                $this->addQueryParam('page', $this->getPage());

                break;
            case 'edit':
                $this->setRouter('zfcadmin/facebook-manager/edit');
                $this->setText(
                    sprintf($this->translate("txt-edit-facebook-%s"), $this->getFacebook()->getFacebook())
                );
                break;
            case 'send-message':
                $this->setRouter('community/contact/facebook/send-message');
                $this->setText(
                    sprintf($this->translate("txt-send-message-to-%s"), $this->getFacebook()->getFacebook())
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/facebook-manager/view');
                $this->setText(
                    sprintf($this->translate("txt-view-facebook-%s"), $this->getFacebook()->getFacebook())
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
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
}
