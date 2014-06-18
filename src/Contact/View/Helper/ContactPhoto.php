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

use Contact\Entity\Contact;
use Contact\Entity\Photo;

/**
 * Create a link to an project
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class ContactPhoto extends HelperAbstract
{
    /**
     * @param Contact $contact
     * @param int     $width
     * @param bool    $responsive
     *
     * @return string
     */
    public function __invoke(Contact $contact, $width = 100, $responsive = true)
    {
        /**
         * @var $photo Photo
         */
        $photo = $contact->getPhoto()->first();
        /**
         * Return an empty photo when there is no, or only a empty object
         */
        if (!$photo || is_null($photo->getId())) {
            return '<img width="' . $width . '" src="assets/itea/style/image/anonymous.jpg" class="img-responsive">';
        }
        $classes = [];
        if ($responsive) {
            $classes[] = 'img-responsive';
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
        $imageUrl = '<img src="%s?%s" width="%s" id="%s" class="%s">';
        $params   = [
            'contactHash' => $photo->getContact()->parseHash(),
            'hash'        => $photo->getHash(),
            'ext'         => $photo->getContentType()->getExtension(),
            'id'          => $photo->getContact()->getId()
        ];
        $image    = sprintf(
            $imageUrl,
            $this->getUrl($router, $params),
            $photo->getDateUpdated()->getTimestamp(),
            $width,
            'contact_photo_' . $contact->getId(),
            implode(' ', $classes)
        );

        return $image;
    }
}
