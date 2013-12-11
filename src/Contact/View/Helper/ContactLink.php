<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
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
     * @param                $action
     * @param                $show
     *
     * @return null|string
     * @throws \Exception
     */
    public function __invoke(Entity\Contact $contact = null, $action = 'view', $show = 'name')
    {
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
            case 'edit':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = sprintf($translate("txt-edit-contact-%s"), $contact);
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


        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<i class="icon-pencil"></i>';
                } elseif ($action === 'delete') {
                    $linkContent[] = '<i class="icon-remove"></i>';
                } else {
                    $linkContent[] = '<i class="icon-info-sign"></i>';
                }
                break;
            case 'button':
                $linkContent[] = '<i class="icon-pencil icon-white"></i> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $contact->getFirstName();
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
