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
class ContactPhoto extends ImageAbstract
{
    /**
     * @param Contact $contact
     * @param int $width
     * @param bool $responsive
     *
     * @return string
     */
    public function __invoke(Contact $contact, $width = null, $responsive = true, $classes = null)
    {
        /**
         * @var $photo Photo
         */
        $photo = $contact->getPhoto()->first();


        if (null !== $classes && !is_array($classes)) {
            $classes = [$classes];
        } elseif (null === $classes) {
            $classes = [];
        }
        if ($responsive) {
            $classes[] = 'img-responsive';
        }
        /**
         * Reset the classes
         */
        $this->setClasses($classes);

        if ($responsive && is_null($height)) {
            $this->addClasses('img-responsive');
        }
        /**
         * Return an empty photo when there is no, or only a empty object
         */
        if (!$photo || is_null($photo->getId())) {
            return sprintf(
                '<img src="assets/' . DEBRANOVA_HOST . '/style/image/anonymous.jpg" class="%s" %s>',
                !($responsive && is_null($height)) ?: implode(' ', ['img-responsive']),
                is_null($height) ?: 'height="' . $height . '"'
            );
        }

        $imageUrl = '<img src="%s?%s" id="%s" class="%s" %s>';
        $params = [
            'contactHash' => $photo->getContact()->parseHash(),
            'hash' => $photo->getHash(),
            'ext' => $photo->getContentType()->getExtension(),
            'id' => $photo->getContact()->getId()
        ];
        $image = sprintf(
            $imageUrl,
            $this->getUrl($router, $params),
            $photo->getDateUpdated()->getTimestamp(),
            'contact_photo_' . $contact->getId(),
            implode(' ', $classes),
            is_null($width) ?: 'width="' . $width . '"'
        );

        $this->setRouter('assets/contact-photo');

        $this->addRouterParam('hash', $photo->getHash());
        $this->addRouterParam('ext', $photo->getContentType()->getExtension());
        $this->addRouterParam('id', $photo->getId());

        $this->setHeight($height);

        return $this->createImageUrl();
    }
}
