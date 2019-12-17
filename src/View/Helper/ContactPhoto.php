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
use General\ValueObject\Image\Image;
use General\ValueObject\Image\ImageDecoration;
use General\View\Helper\AbstractImage;

/**
 * Class ContactPhoto
 *
 * @package Contact\View\Helper
 */
final class ContactPhoto extends AbstractImage
{
    public function __invoke(
        Contact $contact,
        int $width = null,
        string $show = ImageDecoration::SHOW_IMAGE
    ): string {
        $photo = null === $contact->getPhoto() ? false : $contact->getPhoto()->first();

        if (!$photo) {
            return '';
        }

        $linkParams = [];
        $linkParams['route'] = 'image/contact-photo';
        $linkParams['show'] = $show;
        $linkParams['width'] = $width;

        $routeParams = [
            'id' => $photo->getId(),
            'ext' => $photo->getContentType()->getExtension(),
            'last-update' => $photo->getDateUpdated()->getTimestamp(),
        ];

        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Image::fromArray($linkParams));
    }
}
