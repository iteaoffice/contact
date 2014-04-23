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

use Zend\View\Helper\AbstractHelper;

use Contact\Entity;

/**
 * Create a link to an contact
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class ContactLink extends AbstractHelper
{
    /**
     * @param Entity\Contact $contact
     * @param string         $action
     * @param string         $show
     * @param null           $page
     * @param null           $alternativeShow
     *
     * @return Entity\Contact|string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Entity\Contact $contact = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null
    ) {
        $isAllowed = $this->view->plugin('isAllowed');
        $translate = $this->view->plugin('translate');
        $serverUrl = $this->view->plugin('serverUrl');
        $url       = $this->view->plugin('url');

        if (!$isAllowed('contact', $action)) {
            if ($action === 'view' && $show === 'name') {
                return $contact;
            }

            return '';
        }

        $classes     = array();
        $linkContent = array();

        switch ($action) {
            case 'new':
                $router  = 'zfcadmin/contact-manager/new';
                $text    = sprintf($translate("txt-new-contact"));
                $contact = new Entity\Contact();
                break;
            case 'list':
                $router  = 'zfcadmin/contact-manager/list';
                $contact = new Entity\Contact();
                $text    = sprintf($translate("txt-list-contacts"));
                break;
            case 'edit-admin':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = sprintf($translate("txt-edit-contact-%s"), $contact->getDisplayName());
                break;
            case 'profile':
                $router = 'contact/profile';
                $text   = sprintf($translate("txt-view-profile-of-contact-%s"), $contact->getDisplayName());
                break;
            case 'view-admin':
                $router = 'zfcadmin/contact-manager/view';
                $text   = sprintf($translate("txt-view-contact-%s"), $contact->getDisplayName());
                break;
            case 'impersonate':
                $router = 'zfcadmin/contact-manager/impersonate';
                $text   = sprintf($translate("txt-impersonate-contact-%s"), $contact->getDisplayName());
                break;
            case 'edit-profile':
                $router = 'contact/profile-edit';
                $text   = sprintf($translate("txt-edit-your-profile"));
                break;
            case 'opt-in-edit':
                $router = 'contact/opt-in-edit';
                $text   = sprintf($translate("txt-edit-opt-in"));
                break;
            case 'change-password':
                $router = 'contact/change-password';
                /**
                 * Users can have access without a password (via the deeplink)
                 * We will therefore have the option to set a password
                 */
                if (is_null($contact->getSaltedPassword())) {
                    $text      = sprintf($translate("txt-set-your-password"));
                    $classes[] = 'btn-danger';
                } else {
                    $text = sprintf($translate("txt-update-your-password"));
                }

                break;

            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($contact)) {
            throw new \RuntimeException(
                sprintf(
                    "Contact needs to be an instance of %s, %s given in %s",
                    "Contact\Entity\Contact",
                    get_class($contact),
                    __CLASS__
                )
            );
        }

        $params = array(
            'id'     => $contact->getId(),
            'entity' => 'contact'
        );

        $params['page'] = !is_null($page) ? $page : null;

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<span class="glyphicon glyphicon-edit"></span>';
                } else {
                    $linkContent[] = '<span class="glyphicon glyphicon-info-sign"></span>';
                }
                break;
            case 'button':
                $linkContent[] = '<span class="glyphicon glyphicon-info"></span> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $contact->getDisplayName();
                break;
            case 'email':
                $linkContent[] = $contact->getEmail();
                break;
            case 'paginator':

                $linkContent[] = $alternativeShow;
                break;
            default:
                $linkContent[] = $contact;
                break;
        }

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl->__invoke() . $url($router, $params),
            $text,
            implode(' ', $classes),
            implode(' ', $linkContent)
        );
    }
}
