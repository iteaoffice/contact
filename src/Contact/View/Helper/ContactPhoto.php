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
     * @param int     $height
     * @param bool    $responsive
     *
     * @return string
     */
    public function __invoke(Contact $contact, $height = null, $responsive = true)
    {
        /**
         * @var $photo Photo
         */
        $photo = $contact->getPhoto()->first();
        /**
         * Reset the classes
         */
        $this->setClasses([]);

        if ($responsive && is_null($height)) {
            $this->addClasses('img-responsive');
        }
        /**
         * Return an empty photo when there is no, or only a empty object
         */
        if (!$photo || is_null($photo->getId())) {
            return sprintf(
                '<img src="assets/' . DEBRANOVA_HOST . '/style/image/anonymous.jpg" class="%s" %s>',
                implode(' ', ['img-responsive']),
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
