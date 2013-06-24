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
 * Create a link to an equipment
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class MessageLink extends AbstractHelper
{

    /**
     * @param \Contact\Entity\Message $message
     * @param $action
     * @param $show
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\Message $message = null, $action, $show)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $isAllowed = $this->view->plugin('isAllowed');

        $javascript = array();

        if (!$isAllowed('contact', $action)) {
            return '';
        }

        switch ($action) {
            case 'new':
                $router  = 'zfcadmin/contact-manager/new';
                $text    = sprintf($translate("txt-new-message"));
                $message = new Entity\Message();
                break;
            case 'edit':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = $translate("txt-edit-message");
                break;
            case 'delete':
                $javascript[] = ' onclick="return confirm(\'' . $translate(
                    'txt-this-will-remove-the-message-are-you-sure?'
                ) . '\');"';
                $router       = 'zfcadmin/contact-manager/delete';
                $text         = $translate("txt-delete-message");
                break;
            case 'view':
                $router = 'zfcadmin/contact-manager/message';
                $text   = $translate("txt-view-message");
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($message)) {
            throw new \RuntimeException(sprintf(
                "Area needs to be an instance of %s, %s given in %s",
                "Contact\Entity\Message",
                get_class($message),
                __CLASS__
            ));
        }

        $params = array(
            'id'     => $message->getId(),
            'entity' => 'message'
        );

        $classes     = array();
        $linkContent = array();

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
                $linkContent[] = $message;
                break;
            case 'message':
                $linkContent[] = substr($message->getMessage(), 0, 75);
                break;
            default:
                $linkContent[] = $message;
                break;
        }

        $uri = '<a href="%s" title="%s" %s class="%s">%s</a>';

        return sprintf($uri, $url($router, $params), $text, implode($javascript), implode($classes), implode($linkContent));

    }
}
