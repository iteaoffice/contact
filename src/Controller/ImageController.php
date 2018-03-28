<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Photo;
use Zend\Http\Response;

/**
 * The index of the system.
 *
 * @category Content
 */
class ImageController extends ContactAbstractController
{
    /**
     * @return Response
     */
    public function contactPhotoAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $id = $this->params('id');
        if (\is_null($id)) {
            return $response;
        }
        /** @var Photo $photo */
        $photo = $this->getOrganisationService()->findEntityById(Photo::class, $id);

        if (\is_null($photo)) {
            return $response;
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Type: ' . $photo->getContentType()->getContentType());

        $response->setContent(stream_get_contents($photo->getPhoto()));

        return $response;
    }
}
