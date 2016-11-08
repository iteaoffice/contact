<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Photo;

/**
 * Create a link to an project.
 *
 * @category    Contact
 */
class ContactPhoto extends ImageAbstract
{
    /**
     * @param Contact $contact
     * @param null    $height
     * @param bool    $responsive
     * @param null    $classes
     *
     * @return string
     */
    public function __invoke(
        Contact $contact,
        $height = null,
        $responsive = true,
        $classes = null
    ) {
        if (is_null($contact->getPhoto())) {
            return sprintf(
                '<img src="assets/' . ITEAOFFICE_HOST . '/style/image/anonymous.jpg" class="%s" %s>',
                ! ($responsive && is_null($height)) ?: implode(' ', ['img-responsive']),
                is_null($height) ?: 'height="' . $height . '"'
            );
        }

        /**
         * @var Photo $photo
         */
        $photo = $contact->getPhoto()->first();

        if (null !== $classes && ! is_array($classes)) {
            $classes = [$classes];
        } elseif (null === $classes) {
            $classes = [];
        }

        if ($responsive) {
            $classes[] = 'img-responsive';
        }

        $this->setClasses($classes);
        /*
         * Return an empty photo when there is no, or only a empty object
         */
        if (! $photo || is_null($photo->getId())) {
            return sprintf(
                '<img src="assets/' . ITEAOFFICE_HOST . '/style/image/anonymous.jpg" class="%s" %s>',
                ! ($responsive && is_null($height)) ?: implode(' ', ['img-responsive']),
                is_null($height) ?: 'height="' . $height . '"'
            );
        }

        $this->setRouter('assets/contact-photo');

        $this->addRouterParam('hash', $photo->getHash());
        $this->addRouterParam('ext', $photo->getContentType()->getExtension());
        $this->addRouterParam('id', $photo->getId());

        $this->setHeight($height);

        return $this->createImageUrl();
    }
}