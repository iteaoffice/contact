<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Photo;

/**
 * Class ContactPhoto
 * @package Contact\View\Helper
 */
class ContactPhoto extends ImageAbstract
{
    /**
     * @param Contact $contact
     * @param null $width
     * @param bool $responsive
     * @param array $classes
     * @return string
     */
    public function __invoke(
        Contact $contact,
        $width = null,
        $responsive = true,
        $classes = []
    ): string {
        if ($contact->getPhoto()->isEmpty()) {
            return sprintf(
                '<img src="assets/' . ITEAOFFICE_HOST . '/style/image/anonymous.jpg" class="%s" %s>',
                !($responsive && is_null($width)) ?: implode(' ', ['img-responsive']),
                is_null($width) ?: 'width="' . $width . '"'
            );
        }

        /**
         * @var Photo $photo
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

        $this->setClasses($classes);
        $this->setRouter('assets/contact-photo');

        $this->addRouterParam('hash', $photo->getHash());
        $this->addRouterParam('ext', $photo->getContentType()->getExtension());
        $this->addRouterParam('id', $photo->getId());

        if (!is_null($width)) {
            $this->addRouterParam('width', $width);
        }

        return $this->createImageUrl();
    }
}
