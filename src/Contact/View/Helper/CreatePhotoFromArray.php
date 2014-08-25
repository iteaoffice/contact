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

use Contact\Entity\Photo;

/**
 * Class VersionServiceProxy
 * @package General\View\Helper
 */
class CreatePhotoFromArray extends HelperAbstract
{
    /**
     * @param array $PhotoDetails
     *
     * @return Photo
     */
    public function __invoke(array $photoDetails)
    {
        $Photo = new Photo();
        foreach ($photoDetails as $key => $value) {
            $Photo->$key = $value;
        }

        return $Photo;
    }
}
