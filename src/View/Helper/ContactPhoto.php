<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/Contact for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;

/**
 * Class ContactPhoto
 * @package Contact\View\Helper
 */
class ContactPhoto extends ImageAbstract
{
    /**
     * @param Contact $contact
     * @param null $width
     * @param bool $onlyUrl
     * @param bool $responsive
     * @param array $classes
     * @return string
     */
    public function __invoke(
        Contact $contact,
        $width = null,
        $onlyUrl = false,
        $grayscale = true
    ): string {
        $photo = $contact->getPhoto()->first();

        $this->filter = [];

        if (!$photo) {
            return '';
        }

        $this->setRouter('image/contact-photo');

        $this->addRouterParam('ext', $photo->getContentType()->getExtension());
        $this->addRouterParam('last-update', $photo->getDateUpdated()->getTimestamp());
        $this->addRouterParam('id', $photo->getId());

        $this->setImageId('contact_photo_' . $photo->getId());

        if ($grayscale) {
            $this->addFilter('grayscale');
        }

        $this->setWidth($width);

        return $this->createImageUrl($onlyUrl);
    }
}
