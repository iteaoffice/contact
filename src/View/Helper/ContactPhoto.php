<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/Contact for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;

/**
 * Class ContactPhoto
 *
 * @package Contact\View\Helper
 */
final class ContactPhoto extends ImageAbstract
{
    public function __invoke(
        Contact $contact,
        int $width = null,
        int $height = null,
        bool $onlyUrl = false,
        bool $responsive = false,
        bool $grayscale = false
    ): string {
        $this->filter = [];
        $this->classes = [];

        $photo = null === $contact->getPhoto() ? false : $contact->getPhoto()->first();

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
        if ($responsive) {
            $this->addClasses('img-responsive img-fluid');
        }

        if (null !== $width) {
            $this->setWidth($width);
        }
        if (null !== $height) {
            $this->setHeight($height);
        }

        return $this->createImageUrl($onlyUrl);
    }
}
