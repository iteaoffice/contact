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

use Contact\Entity\Contact;

/**
 * Create a link to an project
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class ContactPhoto extends AbstractHelper
{

    /**
     * @param Contact $contact
     *
     * @return string
     */
    public function __invoke(Contact $contact)
    {
        $url   = $this->getView()->plugin('url');
        $photo = $contact->getPhoto();

        if (is_null($photo)) {
            return '<img src="style/image/anonymous.jpg">';
        }

        /**
         * Check if the file is cached and if so, pull it from the assets-folder
         */
        $router = 'contact/photo';
        if (file_exists($photo->getCacheFileName())) {
            /**
             * The file exists, but is it not updated?
             */
            if ($photo->getDateUpdated()->getTimestamp() > filemtime($photo->getCacheFileName())) {
                unlink($photo->getCacheFileName());
            } else {
                $router = 'assets/contact-photo';
            }
        } else {
            file_put_contents(
                $photo->getCacheFileName(),
                is_resource($photo->getPhoto()) ? stream_get_contents($photo->getPhoto()) : $photo->getPhoto()
            );
        }

        $imageUrl = '<img src="%s?%s" id="%s">';

        $params = array(
            'hash' => $photo->getHash(),
            'ext'  => $photo->getContentType()->getExtension(),
            'id'   => $photo->getContact()->getId()
        );


        $image = sprintf(
            $imageUrl,
            $url($router, $params),
            $photo->getDateUpdated()->getTimestamp(),
            'contact_photo_' . $contact->getId()
        );

        return $image;
    }
}
